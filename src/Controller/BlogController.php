<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Forms;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;







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
    public function show(ArticleRepository $repo, $id){
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
        return $this->render('blog/show.html.twig' , [
            'article' => $article
        ]);
    }
}


