<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/admin", name="admin_") */
class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        return $this->render('admin/chart.html.twig');
    }

    /**
     * @Route("/footballeurs", name="footballeur_list")
     */
    public function footballers()
    {
        $em = $this->getDoctrine()->getManager();
        $account_repo = $em->getRepository('App:Account');
        $footballers = $account_repo->getFootballers();
//        dd($footballers);
        return $this->render('admin/footballeurs.html.twig', array(
            'footballers' => $footballers
        ));
    }

    /**
     * @Route("/recruteurs", name="recruteur_list")
     */
    public function recruteurs()
    {
        $em = $this->getDoctrine()->getManager();
        $account_repo = $em->getRepository('App:Account');
        $recruteurs = $account_repo->getrecruteurs();
       // dd($recruteurs);
        return $this->render('admin/recruteurs.html.twig', array(
            'recruteurs' => $recruteurs
        ));
    }

    /**
     * @Route("/setting", name="setting")
     */
    public function setting()
    {
        return $this->render('admin/setting.html.twig');
    }

    /**
     * @Route("/mdp", name="password")
     */
    public function motdepasse()
    {
        return $this->render('admin/motdepasse.html.twig');
    }
}
