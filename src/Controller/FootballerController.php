<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FootballerController extends AbstractController
{
    /**
     * @Route("/show-footballer", name="footballer_home")
     */
    public function footballerHome()
    {
        return $this->render('footballer/index.html.twig', [
            'controller_name' => 'FootballerController',
        ]);
    }

    /**
     * @Route("/show-profil", name="footballer_profil")
     */
    public function index()
    {
        return $this->render('socialNetwork/profil/timeline.html.twig');
    }

    /**
     * @Route("/edit-profil", name="footballer_editProfil")
     */
    public function editProfil()
    {
        return $this->render('socialNetwork/profil/edit-profile-basic.html.twig');
    }

    /**
     * @Route("/edit-interests", name="footballer_editInterests")
     */
    public function changeIntersert()
    {
        return $this->render('socialNetwork/profil/edit-profile-interests.html.twig');
    }

    /**
     * @Route("/change-password", name="footballer_editPassword")
     */
    public function changePassword()
    {
        return $this->render('socialNetwork/profil/edit-profile-password.html.twig');
    }

    /**
     * @Route("/about-profil", name="footballer_aboutProfil")
     */
    public function aboutProfil()
    {
        return $this->render('socialNetwork/profil/timeline-about.html.twig');
    }

    /**
     * @Route("/newsfeed", name="footballer_newsfeed")
     */
    public function newsfeed()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed.html.twig');
    }

    /**
     * @Route("/friend-list", name="footballer_myfriends")
     */
    public function findFriend()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-friends.html.twig');
    }

    /**
     * @Route("/message", name="footballer_message")
     */
    public function mymessage()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-messages.html.twig');
    }

    /**
     * @Route("/add-friend", name="footballer_addfriend")
     */
    public function peopleNearby()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-people-nearby.html.twig');
    }

    /**
     * @Route("/video", name="footballer_video")
     */
    public function myvideo()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-videos.html.twig');
    }

    /**
     * @Route("/picture", name="footballer_picture")
     */
    public function mypicture()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-images.html.twig');
    }
}
