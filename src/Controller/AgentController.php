<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPhotoType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AgentController extends AbstractController
{
    /**
     * @Route("/agent/home", name="agent_home")
     */
    public function index(Request $request, EntityManagerInterface $manager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $gardien = $footballer_repo->findBy(['position' => 'Gardien']);
        $defenseur = $footballer_repo->findBy(['position' => 'Defenseur']);
        $milieu = $footballer_repo->findBy(['position' => 'Milieu']);
        $attaquant = $footballer_repo->findBy(['position' => 'Attaquant']);

        return $this->render('agent/index.html.twig',[
            'gardien' => count($gardien),
            'defenseur' => count($defenseur),
            'milieu' => count($milieu),
            'attaquant' => count($attaquant),
        ]);
    }

    /**
     * @Route("/agent/rechercheFootballer", name="recherche_footballer")
     */
    public function recherche(Request $request, EntityManagerInterface $manager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        //Récupération des infos du formulaire
        $position = $request->request->get('position');
        $age = $request->request->get('age');
        $better_foot = $request->request->get('better-foot');
        $today = new \DateTime();
        $today_2 = new \DateTime();

        $age_tab = explode('-',$age);
        $date_min = $today->modify('-'.$age_tab[0].' years');
        $date_max = $today_2->modify('-'.$age_tab[1].' years');

        $footballers = $footballer_repo->searchFootballersForAgent($position, $date_min, $date_max, $better_foot);

        return $this->render('agent/rechercher-footballer.html.twig',[
            'footballers' => $footballers
        ]);
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
     * @Route("/agent/setting-photo", name="agent_setting_photo")
     */
    public function settingPhoto(Request $request, EntityManagerInterface $manager)
    {
        $user_repo = $manager->getRepository('App:User');
        $user = $user_repo->findOneByAccount($this->getUser());
        if(is_null($user)){
            $user = new User();
        }

        $form = $this->createForm(UserPhotoType::Class, $user);
        $form->handleRequest($request);
        $session = $this->get('session');

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photoProfil')->getData();

            if ($photo) {
                $filesystem = new Filesystem();
                $filesystem->remove($this->getParameter('agent_photo_profil_directory'). '/' .$this->getUser()->getId());
                $newFilename = $this->uploadFile($photo, 'agent_photo_profil_directory', 200);
                $user->setProfilPhoto($newFilename);
                $manager->persist($user);
                $manager->flush();
                $session->set('footballer_profil_photo',$user->getProfilPhoto());
                $this->addFlash('success', 'La photo de profil a été mise à jour');

                return $this->render('agent/setting-photo.html.twig',[
                    'form' => $form->createView()
                ]);
            }
        }

        return $this->render('agent/setting-photo.html.twig',[
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

    /**
     * @Route("/agent/messages/{id}", name="agent_messages",defaults={"id"=0})
     */
    public function messages($id, Request $request, EntityManagerInterface $manager, PublisherInterface $publisher, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $blocked_list_repo = $manager->getRepository('App:BlockFriendsList');
        $user = $this->getUser()->getUser();
        //Récupérer de mes participations
        $participants = $participant_conversations_repo->getParticipants($user);

        //Récupération des conversations dans lesquelles je participe
        $other_participants = [];
        foreach ($participants as $participant) {
            $all_participant = $participant_conversations_repo->getOthersParticipants($user,$participant->getConversation());
            foreach ($all_participant as $item) {
                $other_participants[] = $item;
            }
        }

        $participants = array_merge($participants, $other_participants);
        $conversations = [];

        foreach ($participants as $participant) {
            if($participant->getUser()->getId() != $user->getId()){
                $blocked_list_of_footballer = $blocked_list_repo->getBlockedFootballer($user, $participant);
                if(is_null($blocked_list_of_footballer)){
                    $conversations[$participant->getConversation()->getId()]['participant'] = $participant;
                    $conversations[$participant->getConversation()->getId()]['conversation'] = $participant->getConversation();
                }
            }
        }

        return $this->render('agent/messages.html.twig',[
            'conversations' => $conversations,
            'id' => $id
        ]);
    }

    private function uploadFile($photo, $photo_directory, $width, $photo_compress_directory = null, $width_compressed = 0){
        $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
        // Move the file to the directory where photo are stored
        try {
            $filesystem = new Filesystem();
            $photo->move(
                $this->getParameter($photo_directory). '/' .$this->getUser()->getId(),
                $newFilename
            );

            if($width > 0){
                $manager_picture = Image::make($this->getParameter($photo_directory) . '/' .$this->getUser()->getId(). '/' .$newFilename);
                // to finally create image instances
                $manager_picture->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $manager_picture->save($this->getParameter($photo_directory) . '/' .$this->getUser()->getId(). '/' . $newFilename);

                if(!is_null($photo_compress_directory)){
                    $filesystem->copy(
                        $this->getParameter($photo_directory).'/' .$this->getUser()->getId(). '/'.$newFilename,
                        $this->getParameter($photo_compress_directory).'/' .$this->getUser()->getId(). '/'.$newFilename
                    );
                    //http://image.intervention.io/api/resize
                    $manager_picture2 = Image::make($this->getParameter($photo_compress_directory) . '/' .$this->getUser()->getId(). '/' . $newFilename);
                    $manager_picture2->resize($width_compressed, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $manager_picture2->save($this->getParameter($photo_compress_directory) . '/' .$this->getUser()->getId(). '/' . $newFilename);
                }
            }


            return $newFilename;


        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
    }

    public function t($test){
        dump($test);
        die();
    }

    public function v($test){
        dump($test);
    }
}
