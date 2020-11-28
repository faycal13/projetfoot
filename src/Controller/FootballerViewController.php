<?php

namespace App\Controller;

use App\Entity\Footballer;
use App\Entity\FootballerCarrer;
use App\Entity\FootballerPhoto;
use App\Entity\FootballerVideo;
use App\Entity\FriendsList;
use App\Entity\User;
use App\Form\CoverPhotoFootballerType;
use App\Form\FootballerCareerType;
use App\Form\FootballerPhotoType;
use App\Form\FootballerType;
use App\Form\FootballerVideoType;
use App\Form\ProfilPhotoFootballerType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Intervention\Image\ImageManagerStatic as Image;

/** @Route("/footballer-profil", name="footballer_view_") */
class FootballerViewController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function profil(Footballer $footballer, Request $request, EntityManagerInterface $manager)
    {
        if(!$this->checkBlocked($manager, $footballer)){
            return $this->redirectToRoute('footballer_profil');
        }
        $footballer->setNumberFriends($this->getNumberFriends($manager, $footballer));
        return $this->render('socialNetwork/view/profil.html.twig',[
            'footballer' => $footballer
        ]);
    }

    /**
     * @Route("/footballer-profil/{id}", name="footballer_profil")
     */
    public function footballerProfil(Footballer $footballer, Request $request, EntityManagerInterface $manager)
    {
        if(!$this->checkBlocked($manager, $footballer)){
            return $this->redirectToRoute('footballer_profil');
        }
        $footballer->setNumberFriends($this->getNumberFriends($manager, $footballer));
        return $this->render('socialNetwork/view/footballer-profil.html.twig',[
            'footballer' => $footballer
        ]);
    }

    /**
     * @Route("/career/{id}", name="career")
     */
    public function career(Footballer $footballer, Request $request, EntityManagerInterface $manager)
    {
        if(!$this->checkBlocked($manager, $footballer)){
            return $this->redirectToRoute('footballer_profil');
        }
        $footballer_career_repo = $manager->getRepository('App:FootballerCarrer');
        $footballer_careers = $footballer_career_repo->findByFootballer($footballer, ['startDate' => 'ASC']);
        $footballer->setNumberFriends($this->getNumberFriends($manager, $footballer));
        return $this->render('socialNetwork/view/footballer-career.html.twig',[
            'footballer' => $footballer,
            'careers' => $footballer_careers
        ]);
    }

    /**
     * @Route("/friends/{id}", name="friends")
     */
    public function friends(Footballer $footballer, Request $request, EntityManagerInterface $manager)
    {
        if(!$this->checkBlocked($manager, $footballer)){
            return $this->redirectToRoute('footballer_profil');
        }
        $friends_list_repo = $manager->getRepository('App:FriendsList');

        $blocked_list_repo = $manager->getRepository('App:BlockFriendsList');
        $footballeur_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer_current = $footballeur_repo->findOneByUser($user);
        $blocked_list_of_footballer = $blocked_list_repo->findByFootballer($footballer_current);
        $blocked_list_of_footballer2 = $blocked_list_repo->findByFootballer($footballer);
        $blocked_list_final = array_merge($blocked_list_of_footballer,$blocked_list_of_footballer2);
        $id_footballer_blocked = [];
        foreach ($blocked_list_final as $item) {
            $id_footballer_blocked[] = $item->getTarget()->getId();
        }
        $friends = $friends_list_repo->findBy(array('footballer' => $footballer, 'accept' => 1));
        $friends2 = $friends_list_repo->findBy(array('friend' => $footballer, 'accept' => 1));
        foreach ($friends as $key => $friend) {
            if(!in_array($friend->getFriend()->getId(), $id_footballer_blocked)){
                $friends_final[] = $friend->getFriend();
            }
        }

        foreach ($friends2 as $key => $friend2) {
            if(!in_array($friend2->getFootballer()->getId(), $id_footballer_blocked)) {
                $friends_final[] = $friend2->getFootballer();
            }
        }
        $footballer->setNumberFriends($this->getNumberFriends($manager, $footballer));
        return $this->render('socialNetwork/view/friends-list.html.twig',[
            'footballer' => $footballer,
            'friends' => $friends_final
        ]);
    }

    /**
     * @Route("/picture/{id}", name="picture")
     */
    public function photos(Footballer $footballer, Request $request, EntityManagerInterface $manager)
    {
        if(!$this->checkBlocked($manager, $footballer)){
            return $this->redirectToRoute('footballer_profil');
        }
        $photos_repo = $manager->getRepository('App:FootballerPhoto');
        $photos = $photos_repo->findByFootballer($footballer);
        $footballer->setNumberFriends($this->getNumberFriends($manager, $footballer));

        return $this->render('socialNetwork/view/footballer-photo.html.twig',[
            'footballer' => $footballer,
            'photos' => $photos
        ]);
    }

    /**
     * @Route("/video/{id}", name="video")
     */
    public function videos(Footballer $footballer, Request $request, EntityManagerInterface $manager)
    {
        if(!$this->checkBlocked($manager, $footballer)){
            return $this->redirectToRoute('footballer_profil');
        }
        $videos_repo = $manager->getRepository('App:FootballerVideo');
        $videos = $videos_repo->findByFootballer($footballer);
        foreach ($videos as $video) {
            if(!is_null($video->getExternalLink())){
                $video->setExternalLink($this->convertYoutube($video->getExternalLink()));
            }
        }
        $footballer->setNumberFriends($this->getNumberFriends($manager, $footballer));

        return $this->render('socialNetwork/view/footballer-video.html.twig',[
            'footballer' => $footballer,
            'videos' => $videos,
        ]);
    }

    public function getNumberFriends($manager, $footballer){
        $friends_list_repo = $manager->getRepository('App:FriendsList');
        $blocked_list_repo = $manager->getRepository('App:BlockFriendsList');
        $friends = $friends_list_repo->findBy(array('footballer' => $footballer, 'accept' => 1));
        $friends2 = $friends_list_repo->findBy(array('friend' => $footballer, 'accept' => 1));
        $footballeur_repo = $manager->getRepository('App:Footballer');
        $friends_final = [];
        if(!is_null($this->getUser())){
            $user = $this->getUser()->getUser();
            $footballer_current = $footballeur_repo->findOneByUser($user);
            $blocked_list_of_footballer = $blocked_list_repo->findByFootballer($footballer_current);
            $blocked_list_of_footballer2 = $blocked_list_repo->findByFootballer($footballer);
            $blocked_list_final = array_merge($blocked_list_of_footballer,$blocked_list_of_footballer2);
            $id_footballer_blocked = [];
            foreach ($blocked_list_final as $item) {
                $id_footballer_blocked[] = $item->getTarget()->getId();
            }

            foreach ($friends as $key => $friend) {
                if(!in_array($friend->getFriend()->getId(), $id_footballer_blocked)){
                    $friends_final[] = $friend->getFriend();
                }
            }

            foreach ($friends2 as $key => $friend2) {
                if(!in_array($friend2->getFootballer()->getId(), $id_footballer_blocked)) {
                    $friends_final[] = $friend2->getFootballer();
                }
            }
        }


        return count($friends_final);
    }

    function convertYoutube($string) {
        return preg_replace(
            "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
            "www.youtube.com/embed/$2",
            $string
        );
    }

    function checkBlocked($manager, $footballer){
        $blocked_list_repo = $manager->getRepository('App:BlockFriendsList');
        $footballeur_repo = $manager->getRepository('App:Footballer');
        if(!is_null($this->getUser()) && !in_array('ROLE_AGENT', $this->getUser()->getRoles())){
            $user = $this->getUser()->getUser();
            $footballer_current = $footballeur_repo->findOneByUser($user);
            $blocked_list_of_footballer = $blocked_list_repo->getBlockedFootballer($footballer_current, $footballer);
            if(!is_null($blocked_list_of_footballer)){
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }

    }

    public function t($test){
        dump($test);
        die();
    }

    public function v($test){
        dump($test);
    }
}
