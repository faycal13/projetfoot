<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SocialNetworkController extends AbstractController
{
    /**
 * @Route("/show-profil", name="profil")
 */
    public function index()
    {
        return $this->render('socialNetwork/profil/timeline.html.twig');
    }

    /**
     * @Route("/edit-profil", name="editProfil")
     */
    public function editProfil()
    {
        return $this->render('socialNetwork/profil/edit-profile-basic.html.twig');
    }

    /**
     * @Route("/edit-interests", name="editInterests")
     */
    public function changeIntersert()
    {
        return $this->render('socialNetwork/profil/edit-profile-interests.html.twig');
    }

    /**
     * @Route("/change-password", name="editPassword")
     */
    public function changePassword()
    {
        return $this->render('socialNetwork/profil/edit-profile-password.html.twig');
    }

    /**
     * @Route("/about-profil", name="aboutProfil")
     */
    public function aboutProfil()
    {
        return $this->render('socialNetwork/profil/timeline-about.html.twig');
    }

    /**
     * @Route("/newsfeed", name="newsfeed")
     */
    public function newsfeed()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed.html.twig');
    }

    /**
     * @Route("/friend-list", name="myfriends")
     */
    public function findFriend()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-friends.html.twig');
    }

    /**
     * @Route("/message", name="message")
     */
    public function mymessage()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-messages.html.twig');
    }

    /**
     * @Route("/add-friend", name="addfriend")
     */
    public function peopleNearby()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-people-nearby.html.twig');
    }

    /**
     * @Route("/video", name="video")
     */
    public function myvideo()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-videos.html.twig');
    }

    /**
     * @Route("/picture", name="picture")
     */
    public function mypicture()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-images.html.twig');
    }
}
