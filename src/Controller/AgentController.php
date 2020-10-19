<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AgentController extends AbstractController
{
    /**
     * @Route("/agent/home", name="agent_home")
     */
    public function index()
    {
        return $this->render('agent/index.html.twig');
    }

    /**
     * @Route("/agent/rechercheFootballer", name="recherche_footballer")
     */
    public function recherche()
    {
        return $this->render('agent/rechercher-footballer.html.twig');
    }

    /**
     * @Route("/agent/setting", name="agent_setting")
     */
    public function setting()
    {
        return $this->render('agent/setting.html.twig');
    }

    /**
     * @Route("/agent/mdp", name="agent_password")
     */
    public function motdepasse()
    {
        return $this->render('agent/motdepasse.html.twig');
    }

    /**
     * @Route("/agent/abonnements", name="agent_abonnement")
     */
    public function paiement()
    {
        return $this->render('agent/pricing.html.twig');
    }
}
