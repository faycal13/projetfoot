<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function setting(Request $request, EntityManagerInterface $manager)
    {
        $user_repo = $manager->getRepository('App:User');
        $user = $user_repo->findOneByAccount($this->getUser());
        if(is_null($user)){
            $user = new User();
        }
        $form = $this->createForm(UserType::Class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $today = new \DateTime();
            $user->setAccount($this->getUser());
            $user->setLastModify($today);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Modification effectuée !');

        }
        return $this->render('agent/setting.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/agent/mdp", name="agent_password")
     */
    public function motdepasse(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $params = $request->request->all();
        if(isset($params['button-password'])){
            $account_repo = $manager->getRepository('App:Account');
            $account = $account_repo->findOneById($this->getUser()->getId());
            $hash = $encoder->encodePassword($account, $params['new-password']);
            if($encoder->isPasswordValid($account, $params['old-password'])){
                if($params['new-password'] === $params['confirm-new-password']){
                    if(strlen($params['new-password']) >= 8){
                        $account->setPassword($hash);
                        $manager->persist($account);
                        $manager->flush();
                        $this->addFlash('success', 'Modification effectuée !');
                    }else{
                        $this->addFlash('error', 'Les mots de passe doit contenir au minimum 8 caractères !');
                    }
                }else{
                    $this->addFlash('error', 'Les mots de passe ne sont pas identiques !');
                }
            }else{
                $this->addFlash('error', 'Le mot de passe n\'est pas correct !');
            }
        }
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
