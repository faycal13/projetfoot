<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\PostComments;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostCommentsFixtures extends Fixture
{
    public function load(ObjectManager $manager){
        $post_repo = $manager->getRepository('App:Post');
        $posts = $post_repo->findAll();

        foreach ($posts as $key => $post) {
            for ($i = 0; $i < 2; $i++){
                $post_comment = new PostComments();
                $post_comment->setComment('Le Lorem Ipsum est simplement du faux texte employé dans la 
                composition et la mise en page avant impression. Le Lorem Ipsum est le 
                faux texte standard de l\'imprimerie depuis les années 1500');
                $post_comment->setCreationDate((new \DateTime()));
                $post_comment->setPost($post);
                $idfootballer = rand(1,500);
                $footballer = $manager->getRepository('App:Footballer')->findOneById($idfootballer);
                if(!is_null($footballer)){
                    $post_comment->setFootballer($footballer);
                    $manager->persist($post_comment);
                }
            }
            if($key == 200) $manager->flush();
            if($key == 400) $manager->flush();
            if($key == 600) $manager->flush();
            if($key == 800) $manager->flush();
            if($key == 1000) $manager->flush();
            if($key == 1200) $manager->flush();
            if($key == 1400) $manager->flush();
            if($key == 1600) $manager->flush();
            if($key == 1800) $manager->flush();
            if($key == 2000) $manager->flush();
            if($key == 2000) break;

        }
    }
}
