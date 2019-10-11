<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Créer 3 catégories faké

        for($i = 1; $i <= 3; $i++){
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

                     $manager->persist($category);
                     
             // Créer entre 4 et 6 articles
                     for ($a = 1; $a <= mt_rand(4, 6); $a++) {
                         $article = new Article();
                        // on join les paragraphes car setContent attends une string et pas un array 
                         $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';


                         $article->setTitle($faker->sentence())
                                 ->setContent($content)
                                 ->setImage($faker->imageUrl())
                                 ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                                 ->setCategory($category);

                                 $manager->persist($article);

                      // On donne des commentaires à l'article
                                 for($c = 1 ;$c <= mt_rand(4, 10); $c++ ){
                                     $comment = new Comment();

                                     $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';
                                     $now = new \DateTime();
                                     $interval = $now->diff($article->getCreatedAt());
                                     $days = $interval->days;
                                     $minimum = '-' . $days . ' days'; // -100 days

                                     $comment->setAuthor($faker->name)
                                             ->setContent($content)
                                             ->setCreatedAt($faker->dateTimeBetween($minimum))
                                             ->setArticle($article);
                                     $manager->persist($comment);


                                 }
                         }
        }

        $manager->flush();
    }
}
