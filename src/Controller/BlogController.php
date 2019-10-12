<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Entity\Comment;
use App\Form\CommentType;

use Knp\Component\Pager\PaginatorInterface;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;








class BlogController extends Controller
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo, Request $request)
    {
       
        $allArticles = $repo->findAll();
        $articles = $this->get('knp_paginator')->paginate(
            // Doctrine Query, not results
            $allArticles,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig');
    }
    
    /**
      * @Route("/blog/new", name="blog_create")
      * @Route("/blog/{id}/edit", name="blog_edit")
      */
    public function form(Article $article = null, Request $request, ObjectManager $manager) {
       // $article = new Article();

            if(!$article) {
                $article = new Article;
            }

            $form = $this->createForm(ArticleType::class, $article);
/*
        $form = $this->createFormBuilder($article)
                ->add('title', TextType::class)
                ->add('content', TextareaType::class)
                ->add('image' , TextType::class)          
                ->getForm();
*/
                $form->handleRequest($request);

             if($form->isSubmitted() && $form->isValid()) {
                 if(!$article->getId()){
                    $article->setCreatedAt(new \DateTime);
                 }

                 $manager->persist($article);
                 $manager->flush();

                 return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
             }
       
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, ObjectManager $manager){
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

           $manager->persist($comment);
           $manager->flush();
           return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }
     
  
        return $this->render('blog/show.html.twig' , [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}


