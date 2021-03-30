<?php

namespace App\Controller;

use App\Entity\Footballer;
use App\Entity\Post;
use App\Entity\PostComments;
use App\Entity\PostLikes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Intervention\Image\ImageManagerStatic as Image;

/** @Route("/footballeur-post", name="footballer_post_") */
class PostController extends AbstractController
{
    /**
     * @Route("/feed", name="feed")
     */
    public function feed(Request $request, EntityManagerInterface $manager, PaginatorInterface $paginator)
    {
        if(is_null($this->getUser())) return $this->redirectToRoute('logout');
        $user = $this->getUser()->getUser();
        if(is_null($user)){
            $this->addFlash('error', 'Veuillez renseigner vos informations personnelles');
            return $this->redirectToRoute('footballer_edit_profil');
        }
        $footballer_repo = $manager->getRepository('App:Footballer');
        $footballer = $footballer_repo->findOneByUser($user);
        if(is_null($footballer)){
            $this->addFlash('error', 'Vous devez compléter cette section avant de poursuivre');
            return $this->redirectToRoute('footballer_editFootballerProfil');
        }
        $post_repo = $manager->getRepository('App:Post');
        $post_comment_repo = $manager->getRepository('App:PostComments');
        $posts = $post_repo->findBy(array(), array('creationDate' => 'DESC'), 50);

        return $this->render('socialNetwork/post/newsfeed.html.twig',[
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/my-post", name="my_post")
     */
    public function myPost(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser()->getUser();
        if(is_null($user)){
            $this->addFlash('error', 'Veuillez renseigner vos informations personnelles');
            return $this->redirectToRoute('footballer_edit_profil');
        }
        $footballer_repo = $manager->getRepository('App:Footballer');
        $footballer = $footballer_repo->findOneByUser($user);
        if(is_null($footballer)){
            $this->addFlash('error', 'Vous devez compléter cette section avant de poursuivre');
            return $this->redirectToRoute('footballer_editFootballerProfil');
        }

        $post_repo = $manager->getRepository('App:Post');
        $posts = $post_repo->findBy(array('footballer' => $this->getUser()->getUser()->getFootballer()),array('creationDate' => 'DESC'));
        return $this->render('socialNetwork/post/timeline.html.twig',[
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/add-post", name="add_post")
     */
    public function addPost(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $post_param = $request->request->get('post');
        $url_img = $request->request->get('img');
        $random_img = $request->request->get('random');
        if(!is_null($this->getUser()) && $post_param != ''){
            $footballer = $this->getUser()->getUser()->getFootballer();
            $post = new Post();
            $post->setFootballer($footballer);
            $post->setText($post_param);
            if($url_img != ''){
                $post->setImg('<img style="cursor: pointer" data-toggle="modal" data-target="#img'.$random_img.'" src="'.$url_img.'">');
            }
            $post->setCreationDate((new \DateTime('now')));
            $post->setLiked(0);

            $manager->persist($post);
            $manager->flush();
            $this->addFlash('success', 'Votre publication est en ligne !');
        }else{
            $this->addFlash('error', 'Veuillez saisir un contenu');
        }
        return $this->redirect($request->headers->get('referer'));

    }

    /**
     * @Route("/edit-post", name="edit_post")
     */
    public function editPost(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $post_param = $request->request->get('post-edit');
        $postid_param = $request->request->get('postid');

        if(!is_null($this->getUser()) && $post_param != '' && $postid_param != ''){
            $footballer = $this->getUser()->getUser()->getFootballer();
            $post_manager = $manager->getRepository('App:Post');
            $post = $post_manager->findOneBy(['id'=> $postid_param, 'footballer' => $footballer]);
            if(!is_null($post)){
                $post->setText($post_param);
                $post->setLastModify((new \DateTime('now')));

                $manager->persist($post);
                $manager->flush();
                $this->addFlash('success', 'Votre publication a été modifié !');
            }else{
                $this->addFlash('error', 'Une erreur est intervenue lors de la modification');
            }

            return $this->redirect($request->headers->get('referer'));
        }

    }

    /**
     * @Route("/add-post-img", name="add_post_img")
     */
    public function addPostImg(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $photo = $request->files->get('file');
        if (!is_null($this->getUser()) && $photo) {
            $newFilename = $this->uploadFile($photo, 'footballer_photo_post_directory', 500
            );
            $path = '';
            if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
                isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) {
                $path = $this->getParameter('url_dev');
            }
            $path .= $assetsManager->getUrl('/img/footballer/post/' .$this->getUser()->getId(). '/'. $newFilename);
            echo $path;
            exit;
        }else{
            echo '';
            exit;
        }
        echo '';
        exit;

    }

    /**
     * @Route("/add-comment", name="add_comment")
     */
    public function addComment(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $comment = $request->request->get('comment');
        $postId = $request->request->get('post_id');

        $post_manager = $manager->getRepository('App:Post');
        $post = $post_manager->findOneById($postId);

        if(!is_null($this->getUser()) && !is_null($post)){
            $footballer = $this->getUser()->getUser()->getFootballer();
            if($comment != ''){
                $postComment = new PostComments();
                $postComment->setPost($post);
                $postComment->setComment($comment);
                $postComment->setFootballer($footballer);
                $postComment->setCreationDate((new \DateTime('now')));

                $manager->persist($postComment);
                $manager->flush();
                $path = $this->getProfilPhoto($assetsManager, $this->getUser()->getUser());
                echo json_encode([
                    'result' => true,
                    'name' => $this->getUser()->getUser()->getFirstName() . ' ' . $this->getUser()->getUser()->getName(),
                    'path' => $path,
                    'id' => $postComment->getId(),
                    'footballerid' => $footballer->getId(),
                    'date' => $postComment->getCreationDate()->format('d/m/Y H:i:s'),
                ]);
                exit;
            }else{
                echo json_encode(['result' => false]);
                exit;
            }
        }else{
            echo json_encode(['result' => false]);
            exit;
        }
        echo json_encode(['result' => false]);
        exit;
    }

    /**
     * @Route("/remove-post/{id}", name="remove_post")
     */
    public function removePost(Post $post, Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $footballer = $this->getUser()->getUser()->getFootballer();

        $post_manager = $manager->getRepository('App:Post');
        $post = $post_manager->findOneBy(['id'=> $post->getId(), 'footballer' => $footballer]);

        if(!is_null($this->getUser()) && !is_null($post)){
            $manager->remove($post);
            $manager->flush();
            $this->addFlash('success', 'Votre publication est supprimée');

            return $this->redirect($request->headers->get('referer'));
        }else{
            echo json_encode(['result' => false]);
            exit;
        }
        echo json_encode(['result' => false]);
        exit;
    }

    /**
     * @Route("/remove-comment", name="remove_comment")
     */
    public function removeComment(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $postCommentId = $request->request->get('comment_id');
        $footballer = $this->getUser()->getUser()->getFootballer();

        $post_comments_manager = $manager->getRepository('App:PostComments');
        $post_comments = $post_comments_manager->findOneBy(['id'=> $postCommentId, 'footballer' => $footballer]);

        if(!is_null($this->getUser()) && !is_null($post_comments)){
            $manager->remove($post_comments);
            $manager->flush();
            echo json_encode([
                'result' => true
            ]);
            exit;
        }else{
            echo json_encode(['result' => false]);
            exit;
        }
        echo json_encode(['result' => false]);
        exit;
    }

    /**
     * @Route("/like", name="like")
     */
    public function like(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $like = $request->request->get('like');
        $postId = $request->request->get('post_id');

        if(!is_null($this->getUser()) && $like != '' && $postId != ''){
            $post_manager = $manager->getRepository('App:Post');
            $oldPostLikesRepo = $manager->getRepository('App:PostLikes');

            $footballer = $this->getUser()->getUser()->getFootballer();
            $post = $post_manager->findOneBy(['id'=> $postId]);
            $oldPostLike = $oldPostLikesRepo->findOneBy(['post' => $post, 'footballer' => $footballer]);

            $oldLike = null;
            if(!is_null($oldPostLike)){
                $oldLike = $oldPostLike->getLove();
                $manager->remove($oldPostLike);
                $manager->flush();
            }
            if($oldLike != $like){
                $postLikes = new PostLikes();
                $postLikes->setFootballer($footballer);
                $postLikes->setLove($like);
                $postLikes->setPost($post);
                $postLikes->setCreationDate((new \DateTime('now')));
                $post->setLiked($like);
                $manager->persist($postLikes);
            }else{
                $post->setLiked(0);
            }

            $manager->persist($post);
            $manager->flush();

            $postlikesFinal = $oldPostLikesRepo->findBy(['post' => $post, 'love' => 1]);
            $postDislikesFinal = $oldPostLikesRepo->findBy(['post' => $post, 'love' => 2]);
            echo json_encode([
                'numberlike' => count($postlikesFinal),
                'numberDislike' => count($postDislikesFinal),
                'liked' => $post->getLiked()
            ]);
            exit;
        }
        echo json_encode([
            'result' => false
        ]);
        exit;

    }

    public function getProfilPhoto($assetsManager, $user){
        $path = '';
        if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
            isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) {
            $path = $this->getParameter('url_dev');
        }
        if(in_array('ROLE_AGENT', $user->getAccount()->getRoles())){
            if(!is_null($user->getProfilPhoto())){
                $path .= $assetsManager->getUrl('/img/agent/photo/' .$user->getAccount()->getId(). '/'.$user->getProfilPhoto());
            }else{
                $path .= $assetsManager->getUrl('/img/default/profil-agent.png');
            }
        }else{
            if(!is_null($user->getProfilPhoto())) {
                $path .= $assetsManager->getUrl('/img/user/photo-profil/' . $user->getAccount()->getId() . '/' . $user->getProfilPhoto());
            }
            else{
                $path .= $assetsManager->getUrl('/img/default/profil-footballer.png');
            }
        }
        return $path;
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

