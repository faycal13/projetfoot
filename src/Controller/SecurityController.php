<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index()
    {
        return $this->render('security/login.html.twig');
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
    public function showSignup($who)
    {
        return $this->render('security/signup.html.twig',[
            'who' => $who
        ]);
    }
}
