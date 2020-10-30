<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\ProfilPhotoFootballerType;
use App\Form\SignupType;
use App\Service\CookieGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one

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
            $manager->persist($account);
            $manager->flush();
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($task);
            // $entityManager->flush();

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
    public function redirection(EntityManagerInterface $manager){

        if(!is_null($this->getUser())){
            $role = $this->getUser()->getRoles()[0];
            if($role == 'ROLE_ADMIN') return $this->redirectToRoute('admin_home');
            if($role == 'ROLE_USER') {
                //Enregistrement de variable en session
                $session = $this->get('session');
                $footballer_repo = $manager->getRepository('App:Footballer');
                $friends_list_repo = $manager->getRepository('App:Friendslist');
                $user = $this->getUser()->getUser();
                $footballer = $footballer_repo->findOneByUser($user);
                $response = $this->redirectToRoute('footballer_edit_profil');
                if(!is_null($footballer)){
                    $session->set('footballer_profil_photo',$footballer->getProfilPhoto());
                    $session->set('footballer_cover_photo',$footballer->getCoverPhoto());
                    $session->set('number_friend',count($friends_list_repo->findByFootballer($footballer)));
                    $session->set('footballer_id',$footballer->getId());
                }

                return $response;
            }
            if($role == 'ROLE_AGENT') return $this->redirectToRoute('agent_home');
        }else{
            return $this->redirectToRoute('logout');
        }


    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }

    public function t($test){
        dump($test);
        die();
    }

    public function v($test){
        dump($test);
    }
}
