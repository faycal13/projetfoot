<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager){
//        for ($j = 1; $j <= 500; $j++) {
//            for ($i = 0; $i < 20; $i++){
//                $post = new Post();
//                $post->setText('Le Lorem Ipsum est simplement du faux texte employé dans la
//                composition et la mise en page avant impression. Le Lorem Ipsum est le
//                faux texte standard de l\'imprimerie depuis les années 1500, quand un imprimeur anonyme
//                assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte.
//                Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique,
//                sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles
//                Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications
//                de mise en page de texte, comme Aldus PageMaker.');
//                $post->setCreationDate((new \DateTime()));
//                if(!is_null($manager->getRepository('App:Footballer')->findOneById($j))){
//                    $post->setFootballer($manager->getRepository('App:Footballer')->findOneById($j));
//                    $manager->persist($post);
//                }
//
//            }
//            $manager->flush();
//        }

    }
}
