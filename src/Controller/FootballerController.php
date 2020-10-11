<?php

namespace App\Controller;

use App\Entity\Footballer;
use App\Entity\FootballerCarrer;
use App\Entity\FootballerPhoto;
use App\Entity\FriendsList;
use App\Entity\User;
use App\Form\FootballerCareerType;
use App\Form\FootballerPhotoType;
use App\Form\FootballerType;
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

/** @Route("/footballer", name="footballer_") */
class FootballerController extends AbstractController
{

    /**
     * @Route("/show-footballer", name="home")
     */
    public function footballerHome()
    {
        return $this->render('footballer/index.html.twig', [
            'controller_name' => 'FootballerController',
        ]);
    }

    /**
     * @Route("/profil-photo", name="profil_photo")
     */
    public function footballerProfilPhoto(EntityManagerInterface $manager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        $form = $this->createForm(ProfilPhotoFootballerType::Class, $footballer);

        return $this->render('socialNetwork/profil/profil-photo.html.twig',[
            'form_profil_photo' => $form->createView()
        ]);
    }

    /**
     * @Route("/profil-photo-submission", name="photo_profil_submission")
     */
    public function footballerProfilPhotoSubmission(Request $request, EntityManagerInterface $manager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        $form = $this->createForm(ProfilPhotoFootballerType::Class, $footballer);
        $form->handleRequest($request);
        $session = $this->get('session');
        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('profilPhoto')->getData();
            if ($photo) {
                $newFilename = $this->uploadFile(
                    $footballer, $photo, 'footballer_photo_profil_directory', 200
                );

                $footballer->setProfilPhoto($newFilename);
                $manager->persist($footballer);
                $manager->flush();
                $session->set('footballer_profil_photo',$footballer->getProfilPhoto());
                $this->addFlash('success', 'La photo de profil a été mise à jour');
                return $this->redirectToRoute('footballer_editProfil');
            }
        }


        return $this->render('socialNetwork/profil/profil-photo.html.twig',[
            'form_profil_photo' => $form->createView()
        ]);
    }

    /**
     * @Route("/show-profil", name="profil")
     */
    public function index()
    {
        return $this->render('socialNetwork/profil/timeline.html.twig');
    }

    /**
     * @Route("/edit-profil", name="editProfil")
     */
    public function editProfil(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
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
        return $this->render('socialNetwork/profil/edit-profile-basic.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit-footballer-profil", name="editFootballerProfil")
     */
    public function editFootballerProfil(Request $request, EntityManagerInterface $manager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        if(is_null($user)){
            $this->addFlash('error', 'Veuillez renseigner vos informations personnelles');
            return $this->redirectToRoute('footballer_editProfil');
        }
        $footballer = $footballer_repo->findOneByUser($this->getUser()->getUser());
        if(is_null($footballer)){
            $footballer = new Footballer();
        }
        $form = $this->createForm(FootballerType::Class, $footballer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $footballer->setUser($user);
            $manager->persist($footballer);
            $manager->flush();
            $this->addFlash('success', 'Modification effectuée !');

        }
        return $this->render('socialNetwork/profil/edit-profil-footballer.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit-career", name="editCareer")
     */
    public function editCareer(Request $request, EntityManagerInterface $manager)
    {
        $footballer_career_repo = $manager->getRepository('App:FootballerCarrer');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        if(is_null($user)){
            $this->addFlash('error', 'Veuillez renseigner vos informations personnelles');
            return $this->redirectToRoute('footballer_editProfil');
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
     * @Route("/edit-career-submission/{id}", name="editCareerSubmission")
     */
    public function editCareerSubmission(FootballerCarrer $footballer_career, Request $request, EntityManagerInterface $manager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);

        $form = $this->createForm(FootballerCareerType::Class, $footballer_career);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $footballer_career->setFootballer($footballer);
            $manager->persist($footballer_career);
            $manager->flush();
            $this->addFlash('success', 'Le club a été modifié !');
        }

        return $this->redirectToRoute('footballer_editCareer');
    }

    /**
     * @Route("/edit-career-delete/{id}", name="editCareerDelete")
     */
    public function editCareerDelete(FootballerCarrer $footballer_career, Request $request, EntityManagerInterface $manager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);

        if($footballer_career->getFootballer()->getId() == $footballer->getId()){
            $manager->remove($footballer_career);
            $manager->flush();
            $this->addFlash('success', 'Le club a été supprimé !');
        }else{
            $this->addFlash('error', 'Une erreur est survenue !');
        }


        return $this->redirectToRoute('footballer_editCareer');
    }

    /**
     * @Route("/change-password", name="editPassword")
     */
    public function changePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
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

        return $this->render('socialNetwork/profil/edit-profile-password.html.twig');
    }

    /**
     * @Route("/about-profil", name="aboutProfil")
     */
    public function aboutProfil()
    {
        return $this->render('socialNetwork/profil/timeline-about.html.twig');
    }

    /**
     * @Route("/newsfeed", name="newsfeed")
     */
    public function newsfeed()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed.html.twig');
    }

    /**
     * @Route("/friend-list", name="myfriends")
     */
    public function myFriends(Request $request, EntityManagerInterface $manager)
    {
        $friends_list_repo = $manager->getRepository('App:FriendsList');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        $friends = $friends_list_repo->findByFootballer($footballer);

        return $this->render('socialNetwork/newsfeed/friendsList.html.twig',[
            'friends' => $friends
        ]);
    }

    /**
     * @Route("/message", name="message")
     */
    public function mymessage()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-messages.html.twig');
    }

    /**
     * @Route("/chatroom", name="chatroom")
     */
    public function mychatroom()
    {
        return $this->render('socialNetwork/newsfeed/chatroom.html.twig');
    }

    /**
     * @Route("/chatroom-message", name="chatroom-message")
     */
    public function mymessagechatroom()
    {
        return $this->render('socialNetwork/newsfeed/chatroom-messages.html.twig');
    }


    /**
     * @Route("/add-friend", name="addfriend")
     */
    public function peopleNearby(Request $request, EntityManagerInterface $manager)
    {
        $friends_list_repo = $manager->getRepository('App:FriendsList');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        $friends = $friends_list_repo->findByFootballer($footballer);

        return $this->render('socialNetwork/newsfeed/friendsNearbyList.html.twig',[
            'friends' => $friends
        ]);
    }

    /**
     * @Route("/add-friend-submission", name="add_friend_submission")
     */
    public function addFriendSubmission(Request $request, EntityManagerInterface $manager)
    {
        $id = $request->request->get('id');
        $user = $this->getUser()->getUser();
        $footballer_repo = $manager->getRepository('App:Footballer');
        $footballer_current = $footballer_repo->findOneByUser($user);
        $footballer_friend = $footballer_repo->findOneById($id);

        $friend = new FriendsList();
        $friend->setFootballer($footballer_current);
        $friend->setFriend($footballer_friend);
        $friend->setCreationDate((new \DateTime('now')));
        $manager->persist($friend);
        $manager->flush();

        return new JsonResponse(['result' => true]);
    }

    /**
     * @Route("/video", name="video")
     */
    public function myvideo()
    {
        return $this->render('socialNetwork/newsfeed/newsfeed-videos.html.twig');
    }

    /**
     * @Route("/picture", name="picture")
     */
    public function myPhotos (Request $request, EntityManagerInterface $manager)
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
     * @Route("/photo-delete/{id}", name="photoDelete")
     */
    public function photoCareerDelete(FootballerPhoto $footballer_photo, Request $request, EntityManagerInterface $manager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);

        if($footballer_photo->getFootballer()->getId() == $footballer->getId()){
            $manager->remove($footballer_photo);
            $manager->flush();
            $this->addFlash('success', 'La photo a été supprimé !');
        }else{
            $this->addFlash('error', 'Une erreur est survenue !');
        }

        return $this->redirectToRoute('footballer_picture');
    }

    private function uploadFile($footballer, $photo, $photo_directory, $width, $photo_compress_directory = null, $width_compressed = 0){
        $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
        // Move the file to the directory where photo are stored
        try {
            $filesystem = new Filesystem();
            $photo->move(
                $this->getParameter($photo_directory). '/' .$footballer->getId(),
                $newFilename
            );

            $manager_picture = Image::make($this->getParameter($photo_directory) . '/' .$footballer->getId(). '/' .$newFilename);
// to finally create image instances
            $manager_picture->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $manager_picture->save($this->getParameter($photo_directory) . '/' .$footballer->getId(). '/' . $newFilename);

            if(!is_null($photo_compress_directory)){
                $filesystem->copy(
                    $this->getParameter($photo_directory).'/'.$newFilename,
                    $this->getParameter($photo_compress_directory).'/'.$newFilename
                );
                //http://image.intervention.io/api/resize
                $manager_picture2 = Image::make($this->getParameter($photo_compress_directory) . '/' .$footballer->getId(). '/' . $newFilename);
                $manager_picture2->resize($width_compressed, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $manager_picture2->save($this->getParameter($photo_compress_directory) . '/' .$footballer->getId(). '/' . $newFilename);
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
