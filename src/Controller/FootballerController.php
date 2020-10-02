<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** @Route("/footballer", name="footballer_") */
class FootballerController extends AbstractController
{
    /**
     * @Route("/show-footballer", name="home")
     */
    public function footballerHome()
    {
        return $this->render('footballer/index.html.twig', [
            'controller_name' => 'FootballerController',
        ]);
    }

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
    public function editProfil(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user_repo = $manager->getRepository('App:User');
        $user = $user_repo->findOneByAccount($this->getUser());
        if(is_null($user)){
            $user = new User();
        }
        $form = $this->createForm(UserType::Class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $today = new \DateTime();
            $user->setAccount($this->getUser());
            $user->setLastModify($today);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Modification effectuée !');

        }
        return $this->render('socialNetwork/profil/edit-profile-basic.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit-interests", name="editCareer")
     */
    public function changeIntersert()
    {
        return $this->render('socialNetwork/profil/edit-profile-interests.html.twig');
    }

    /**
     * @Route("/change-password", name="editPassword")
     */
    public function changePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $params = $request->request->all();
        if(isset($params['button-password'])){
            $account_repo = $manager->getRepository('App:Account');
            $account = $account_repo->findOneById($this->getUser()->getId());
            $hash = $encoder->encodePassword($account, $params['new-password']);
            if($encoder->isPasswordValid($account, $params['old-password']) && ($params['new-password'] === $params['confirm-new-password'])){
                if(strlen($params['new-password']) >= 8){
                    $account->setPassword($hash);
                    $manager->persist($account);
                    $manager->flush();
                    $this->addFlash('success', 'Modification effectuée !');
                }else{
                    $this->addFlash('error', 'Le mot de passe doit contenir au minimum 8 caractères !');
                }

            }else{
                $this->addFlash('error', 'Erreur lors de la modification');
            }
        }

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
     * @Route("/chatroom", name="chatroom")
     */
    public function mychatroom()
    {
        return $this->render('socialNetwork/newsfeed/chatroom.html.twig');
    }

    /**
     * @Route("/chatroom-message", name="chatroom-message")
     */
    public function mymessagechatroom()
    {
        return $this->render('socialNetwork/newsfeed/chatroom-messages.html.twig');
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

    public function t($test){
        dump($test);
        die();
    }

    public function v($test){
        dump($test);
    }
}
