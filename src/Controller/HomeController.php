<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('contact.html.twig');
    }

    /**
     * @Route("/qui-sommes-nous", name="about-us")
     */
    public function aboutUs()
    {
        return $this->render('about.html.twig');
    }

    /**
     * @Route("/nos-tarifs", name="pricing")
     */
    public function pricing()
    {
        return $this->render('pricing.html.twig');
    }
}
