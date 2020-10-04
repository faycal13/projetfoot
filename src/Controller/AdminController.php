<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function index()
    {
        return $this->render('admin/chart.html.twig');
    }

    /**
     * @Route("/admin/footballeurs", name="footballeur_list")
     */
    public function footballers()
    {

        $em = $this->getDoctrine()->getManager();
        $account_repo = $em->getRepository('App:Account');
        $footballers = $account_repo->getFootballers();
        dd($footballers[0].getUser());
        return $this->render('admin/footballeurs.html.twig');
    }

    /**
     * @Route("/admin/recruteurs", name="recruteur_list")
     */
    public function recruteurs()
    {
        return $this->render('admin/recruteurs.html.twig');
    }
}
