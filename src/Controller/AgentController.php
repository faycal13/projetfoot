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
}
