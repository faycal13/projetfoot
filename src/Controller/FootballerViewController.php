<?php

namespace App\Controller;

use App\Entity\Footballer;
use App\Entity\FootballerCarrer;
use App\Entity\FootballerPhoto;
use App\Entity\FootballerVideo;
use App\Entity\FriendsList;
use App\Entity\User;
use App\Form\CoverPhotoFootballerType;
use App\Form\FootballerCareerType;
use App\Form\FootballerPhotoType;
use App\Form\FootballerType;
use App\Form\FootballerVideoType;
use App\Form\ProfilPhotoFootballerType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Intervention\Image\ImageManagerStatic as Image;

/** @Route("/footballer/view/", name="footballer_view_") */
class FootballerViewController extends AbstractController
{
    /**
     * @Route("/{id}/profil", name="profil")
     */
    public function profil(Footballer $footballer, Request $request, EntityManagerInterface $manager)
    {
        return $this->render('socialNetwork/view/profil.html.twig',[
            'footballer' => $footballer
        ]);
    }

    /**
     * @Route("/career", name="career")
     */
    public function career(Request $request, EntityManagerInterface $manager)
    {
        $footballer_career_repo = $manager->getRepository('App:FootballerCarrer');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        if(is_null($user)){
            $this->addFlash('error', 'Veuillez renseigner vos informations personnelles');
            return $this->redirectToRoute('footballer_edit_profil');
        }
        $footballer = $footballer_repo->findOneByUser($user);
        if(is_null($footballer)){
            $this->addFlash('error', 'Vous devez compléter remplir cette section avant de poursuivre');
            return $this->redirectToRoute('footballer_editFootballerProfil');
        }

        $footballer_careers = $footballer_career_repo->findByFootballer($footballer, ['startDate' => 'ASC']);
        $forms = [];
        foreach ($footballer_careers as $footballer_career) {
            $form = $this->createForm(FootballerCareerType::Class, $footballer_career);
            $forms[$footballer_career->getId()] = $form->createView();
        }

        $new_footballer_career = new FootballerCarrer();
        $new_form = $this->createForm(FootballerCareerType::Class, $new_footballer_career);
        $new_form->handleRequest($request);
        if ($new_form->isSubmitted() && $new_form->isValid()) {
            $new_footballer_career->setFootballer($footballer);
            $manager->persist($new_footballer_career);
            $manager->flush();
            $this->addFlash('success', 'Le club a été ajouté !');

        }

        return $this->render('socialNetwork/profil/edit-career-footballer.html.twig',[
            'forms' => $forms,
            'new_form' => $new_form->createView(),
            'careers' => $footballer_careers
        ]);
    }

    /**
     * @Route("/friend", name="friend")
     */
    public function friends(Request $request, EntityManagerInterface $manager)
    {
        $friends_list_repo = $manager->getRepository('App:FriendsList');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        $friends = $friends_list_repo->findBy(array('footballer' => $footballer, 'accept' => 1));

        return $this->render('socialNetwork/newsfeed/friendsList.html.twig',[
            'friends' => $friends
        ]);
    }

    /**
     * @Route("/picture", name="picture")
     */
    public function photos(Request $request, EntityManagerInterface $manager)
    {
        $photos_repo = $manager->getRepository('App:FootballerPhoto');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        $photos = $photos_repo->findByFootballer($footballer);

        $footballer_photo = new FootballerPhoto();

        $form = $this->createForm(FootballerPhotoType::Class, $footballer_photo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('internalLink')->getData();
            if ($photo) {
                $newFilename = $this->uploadFile(
                    $footballer, $photo, 'footballer_photo_directory', 1500, 'footballer_photo_compressed_directory', 800
                );
                $footballer_photo->setFootballer($footballer);
                $footballer_photo->setInternalLink($newFilename);
                $footballer_photo->setCreationDate((new \DateTime()));
                $manager->persist($footballer_photo);
                $manager->flush();
                $this->addFlash('success', 'La photo a été ajouté');
                return $this->redirectToRoute('footballer_picture');
            }else{
                $this->addFlash('error', 'Une erreur est survenue');
            }

        }

        return $this->render('socialNetwork/newsfeed/footballer-photo.html.twig',[
            'photos' => $photos,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/video", name="video")
     */
    public function videos(Request $request, EntityManagerInterface $manager)
    {
        $videos_repo = $manager->getRepository('App:FootballerVideo');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        $videos = $videos_repo->findByFootballer($footballer);
        foreach ($videos as $video) {
            if(!is_null($video->getExternalLink())){
                $video->setExternalLink($this->convertYoutube($video->getExternalLink()));
            }
        }

        $footballer_video = new FootballerVideo();

        $form = $this->createForm(FootballerVideoType::Class, $footballer_video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $video = $form->get('internalLink')->getData();
            if ($video || $form->get('externalLink')) {
                if(!is_null($video)){
                    $newFilename = $this->uploadFile(
                        $footballer, $video, 'footballer_video_directory', 0
                    );
                }else{
                    $newFilename = null;
                }

                $footballer_video->setFootballer($footballer);
                $footballer_video->setInternalLink($newFilename);
                $footballer_video->setCreationDate((new \DateTime()));
                $manager->persist($footballer_video);
                $manager->flush();
                $this->addFlash('success', 'La video a été ajouté');
                return $this->redirectToRoute('footballer_video');
            }else{
                $this->addFlash('error', 'Une erreur est survenue');
            }

        }

        return $this->render('socialNetwork/newsfeed/footballer-video.html.twig',[
            'videos' => $videos,
            'form' => $form->createView()
        ]);
    }

    public function t($test){
        dump($test);
        die();
    }

    public function v($test){
        dump($test);
    }
}
