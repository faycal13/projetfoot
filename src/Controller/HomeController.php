<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

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
    public function contact(Request $request)
    {
        if($request->request->get('email') != '' && $request->request->get('message') != '' && $request->request->get('phone') != '' && $request->request->get('name') != ''){
            $message = (new \Swift_Message())
                ->setFrom('contact@skillfoot.fr')
                ->setTo('contact@skillfoot.fr')
                ->setSubject('Formulaire de contact | SkillFoot');

            $img = $message->embed(\Swift_Image::fromPath('img/logo/logo.png'));
            $message->setBody(
                $this->renderView(
                    'mail.html.twig',
                    [
                        'img' => $img,
                        'titre' => 'Formulaire de contact',
                        'message' => '
                        <p style="font-size: 18px">Nom : '.$request->request->get('email').'</p>
                        <p style="font-size: 18px">Mail : '.$request->request->get('email').'</p>
                        <p style="font-size: 18px">Téléphone : '.$request->request->get('phone').'</p>
                        <p style="font-size: 18px">Message : '.$request->request->get('message').'</p>
                        <p style="font-size: 18px"><a style="color: white" href="https://skillfoot.fr/login">Cliquez-ici pour vous connecter.</a></p>
                        '
                    ]
                ),
                'text/html'
            );

            $this->mailer->send($message);
            $this->addFlash('success', 'Le message a été envoyé. Nous vous contacterons dans les plus brefs délais.');
        }
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

    /**
     * @Route("/paiement", name="paiement")
     */
    public function paiement()
    {
        return $this->render('paiement.html.twig');
    }
}
