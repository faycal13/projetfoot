<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\ProfilPhotoFootballerType;
use App\Form\SignupType;
use App\Service\CookieGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if(!is_null($this->getUser())) {
            return $this->redirectToRoute('redirection');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/footballeur-ou-agent", name="pre_signup")
     */
    public function preSignup()
    {
        return $this->render('security/pre-signup.html.twig');
    }

    /**
     * @Route("/inscription/{who}", name="signup")
     */
    public function showSignup($who, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $account = new Account();
        $form = $this->createForm(SignupType::Class, $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            if($who == 'agent') $role = ['ROLE_AGENT'];
            else if($who == 'footballer') $role = ['ROLE_USER'];
            else $role = [];
            $hash = $encoder->encodePassword($account, $account->getPassword());
            $today = new \DateTime();
            $account->setPassword($hash);
            $account->setRoles($role);
            $account->setCreationDate($today);
            $account->setOnline(0);
            $account->setIsDelete(0);
            $manager->persist($account);
            $manager->flush();

//            $this->mail(
//                $account->getUsername(),
//                'Inscription SkillFoot | Skillfoot',
//                'Inscription SkillFoot',
//                '
//                <p style="font-size: 18px">Bienvenue chez SkillFoot,</p>
//                <p style="font-size: 18px">Vous venez d\'effectuer votre inscription sur la plateforme skillfoot.fr.</p>
//                <p style="font-size: 18px">Connectez-vous dès maintenant et profitez de l\'expérience skillfoot !</p>
//                <p style="font-size: 18px"><a style="color: white" href="https://skillfoot.fr/login">Cliquez-ici pour vous connecter.</a></p>
//                ',
//                $mailer
//            );

            return $this->render('security/post-signup.html.twig');
        }

        return $this->render('security/signup.html.twig',[
            'who' => $who,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/redirection", name="redirection")
     */
    public function redirection(EntityManagerInterface $manager, PublisherInterface $publisher, \Symfony\Component\Asset\Packages $assetsManager){

        if($this->getUser()->getIsDelete() == 1){
            return $this->redirectToRoute('logout');
        }
        if(!is_null($this->getUser())){
            $role = $this->getUser()->getRoles()[0];
            if($role == 'ROLE_ADMIN') return $this->redirectToRoute('admin_home');
            if($role == 'ROLE_USER') {
                //Enregistrement de variable en session
                $session = $this->get('session');
                $footballer_repo = $manager->getRepository('App:Footballer');
                $account_repo = $manager->getRepository('App:Account');
                $friends_list_repo = $manager->getRepository('App:FriendsList');
                $user = $this->getUser()->getUser();
                $account = $account_repo->findOneById($this->getUser()->getId());
                $account->setOnline(1);
                $manager->persist($account);
                $manager->flush();
                $footballer = $footballer_repo->findOneByUser($user);
                $response = $this->redirectToRoute('footballer_post_feed');
                if(!is_null($footballer)){

                    $friends = $friends_list_repo->findBy(array('footballer' => $footballer, 'accept' => 1));
                    $friends_2 = $friends_list_repo->findBy(array('friend' => $footballer, 'accept' => 1));
                    $session->set('footballer_profil_photo',$user->getProfilPhoto());
                    $session->set('footballer_cover_photo',$user->getCoverPhoto());
                    $session->set('number_friend',count($friends_list_repo->findByFootballer($footballer)));
                    $session->set('footballer_id',$footballer->getId());

                    $friend_tab = [];
                    //Mise en ligne du compte et affichage pour les ami qui possède
                    foreach ($friends as $friend) {
                        $path = '';
                        if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
                            isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev');
                        if(!is_null($friend->getFootballer()->getUser()->getProfilPhoto())){
                            $path .= $assetsManager->getUrl('/img/user/photo-profil/' .$friend->getFootballer()->getUser()->getAccount()->getId(). '/'.$friend->getFootballer()->getUser()->getProfilPhoto());
                        }else{
                            $path .= $assetsManager->getUrl('/img/default/profil-footballer.png');
                        }
                        $friend_tab['nom-prenom'] = $friend->getFootballer()->getUser()->getName().' '.$friend->getFootballer()->getUser()->getFirstName();
                        $friend_tab['photo'] = $path;
                        $friend_tab['id'] = $friend->getFootballer()->getUser()->getAccount()->getId();

                        $update = new Update('http://skillfoot.fr/users/online/'.$friend->getFriend()->getId(), json_encode($friend_tab));
                        $publisher($update);
                    }

                    //Mise en ligne du compte et affichage pour ceux qui l'ont comme ami
                    foreach ($friends_2 as $friend_2) {
                        $path = '';
                        if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
                            isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev');
                        if(!is_null($friend_2->getFriend()->getUser()->getProfilPhoto())){
                            $path .= $assetsManager->getUrl('/img/footballer/photo-profil/' .$friend_2->getFriend()->getUser()->getAccount()->getId(). '/'.$friend_2->getFriend()->getUser()->getProfilPhoto());
                        }else{
                            $path .= $assetsManager->getUrl('/img/default/profil-footballer.png');
                        }
                        $friend_tab['nom-prenom'] = $friend_2->getFriend()->getUser()->getName().' '.$friend_2->getFriend()->getUser()->getFirstName();
                        $friend_tab['photo'] = $path;
                        $friend_tab['id'] = $friend_2->getFriend()->getUser()->getAccount()->getId();
                        $update = new Update('http://skillfoot.fr/users/online/'.$friend_2->getFootballer()->getId(), json_encode($friend_tab));
                        $publisher($update);
                    }
                }else{
                    $response = $this->redirectToRoute('footballer_edit_profil');
                }

                return $response;
            }
            if($role == 'ROLE_AGENT') return $this->redirectToRoute('agent_home');
        }
        else{
            return $this->redirectToRoute('logout');
        }
    }

    /**
     * @Route("/before-logout", name="before_logout")
     */
    public function beforeLogout(EntityManagerInterface $manager, PublisherInterface $publisher){
        $friends_list_repo = $manager->getRepository('App:FriendsList');
        $account_repo = $manager->getRepository('App:Account');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        $account = $account_repo->findOneById($this->getUser()->getId());
        $account->setOnline(0);
        $manager->persist($account);
        $manager->flush();

        $friends = $friends_list_repo->findBy(array('footballer' => $footballer, 'accept' => 1));
        $friends_2 = $friends_list_repo->findBy(array('friend' => $footballer, 'accept' => 1));

        $friend_tab = [];
        //Mise en ligne du compte et affichage pour les ami qui possède
        foreach ($friends as $friend) {
            $friend_tab['id'] = $friend->getFootballer()->getUser()->getAccount()->getId();
            $update = new Update('http://skillfoot.fr/users/logout/'.$friend->getFriend()->getId(), json_encode($friend_tab));
            $publisher($update);
        }

        //Mise en ligne du compte et affichage pour ceux qui l'ont comme ami
        foreach ($friends_2 as $friend_2) {
            $friend_tab['id'] = $friend_2->getFriend()->getUser()->getAccount()->getId();
            $update = new Update('http://skillfoot.fr/users/logout/'.$friend_2->getFootballer()->getId(), json_encode($friend_tab));
            $publisher($update);
        }

        return $this->redirectToRoute('logout');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){
    }

    /**
     * @Route("/mot-de-passe-oublie", name="forgot_password")
     */
    public function forgotPassword(EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $email = $request->request->get('email');
        if(!is_null($email)){
            $account_repo = $manager->getRepository('App:Account');
            $account = $account_repo->findOneByUsername($email);
            if(!is_null($account)){
                //Envoi du mail avec un mot de passe fictif
                $new_password = substr(str_shuffle(MD5(microtime())), 0, 10);
                $encoded = $encoder->encodePassword($account, $new_password);
                $account->setPassword($encoded);
                $manager->persist($account);
                $manager->flush();
                //Envoi du mail mot de passe oublié
//                $this->mail(
//                    $email,
//                    'Réinitialisation du mot de passe | Skillfoot',
//                    'Réinitialisation du mot de passe',
//                    'Bonjour, <br>Votre nouveau mot de passe est : '.$new_password.' <br> Connectez-vous pour le modifier.'
//                );
            }
            $this->addFlash('success_forgot', 'Un email vous a été envoyé, si votre compte est existant.');
        }
        return $this->render('security/forgot-password.html.twig');
    }

    public function t($test){
        dump($test);
        die();
    }

    public function v($test){
        dump($test);
    }

//    function mail($mail, $objet, $titre, $contain, $mailer)
//    {
//        $message = (new \Swift_Message())
//            ->setFrom('noreply@hskillfoot.fr')
//            ->setTo($mail)
//            ->setSubject($objet);
//
//        $img = $message->embed(\Swift_Image::fromPath('img/logo/logo.png'));
//        $message->setBody(
//            $this->renderView(
//            // templates/emails/registration.html.twig
//                'mail.html.twig',
//                [
//                    'img' => $img,
//                    'titre' => $titre,
//                    'message' => $contain,
//                ]
//            ),
//            'text/html'
//        );
//
//        $mailer->send($message);
//    }
}
