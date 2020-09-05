<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FootballerController extends AbstractController
{
    /**
     * @Route("/show-footballer", name="footballer")
     */
    public function index()
    {
        return $this->render('footballer/index.html.twig', [
            'controller_name' => 'FootballerController',
        ]);
    }
}
