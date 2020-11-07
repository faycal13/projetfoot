<?php

namespace App\Controller;

use App\Entity\PrivateMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/footballer-social-network", name="footballer_social_network_") */
class SocialNetworkController extends AbstractController
{
    /**
     * @Route("/show-conversations", name="show_conversations")
     */
    public function showConversation(Request $request, EntityManagerInterface $manager, PublisherInterface $publisher, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        //Récupérer de mes participations
        $participants = $participant_conversations_repo->getParticipants($footballer);
        //Récupération des conversations dans lesquelles je participe
        $other_participants = [];
        foreach ($participants as $participant) {
            $all_participant = $participant_conversations_repo->getOthersParticipants($footballer,$participant->getConversation());
            foreach ($all_participant as $item) {
                $other_participants[] = $item;
            }
        }
        $participants = array_merge($participants, $other_participants);
        $conversations = [];
        foreach ($participants as $participant) {
            if($participant->getFootballer()->getId() != $footballer->getId()){
                $conversations[$participant->getConversation()->getId()]['participant'] = $participant->getFootballer();
                $conversations[$participant->getConversation()->getId()]['conversation'] = $participant->getConversation();
            }
        }
        return $this->render('socialNetwork/newsfeed/show-conversation.html.twig',[
            'conversations' => $conversations
        ]);
    }

    /**
     * @Route("/get-messages", name="get_messages")
     */
    public function getMessages(Request $request, EntityManagerInterface $manager, PublisherInterface $publisher, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $conversation_id = $request->request->get('conversation');
        $footballer_repo = $manager->getRepository('App:Footballer');
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $private_message_repo = $manager->getRepository('App:PrivateMessage');
        $user = $this->getUser()->getUser();
        $footballer = $footballer_repo->findOneByUser($user);
        //Vérifier si j'y participe
        $participation = $participant_conversations_repo->getMyParticipation($footballer, $conversation_id);
        if(!is_null($participation)){
            //Récupération des messages
            $messages = $private_message_repo->findByConversation($conversation_id);
            //formation du retour : sender (left ou right), nom prénom et photo, message
            $final_message = [];
            foreach ($messages as $key => $message) {
                $date = $message->getCreationDate();
                $final_message[$key]['date'] = $date->format('d/m/Y H:i:s');
                $final_message[$key]['message'] = $message->getMessage();
                $final_message[$key]['nom'] = $message->getSender()->getUser()->getName().' '.$message->getSender()->getUser()->getFirstName();
                $final_message[$key]['position'] = ($message->getSender()->getId() == $footballer->getId() ? 'right' : 'left');
                if(strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev');
                $path .= $assetsManager->getUrl('/img/footballer/photo-profil/' .$message->getSender()->getUser()->getAccount()->getId(). '/'.$message->getSender()->getProfilPhoto());
                $final_message[$key]['photo'] = '<img src="'.$path.'" alt="" class="profile-photo-sm pull-'.$final_message[$key]['position'].'"/>';
            }
            return new JsonResponse(['result' => $final_message]);
        }

        return new JsonResponse(['result' => false]);
    }

    /**
     * @Route("/add-message", name="add_message", methods={"POST"})
     */
    public function chatroomMessageAdd(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager, PublisherInterface $publisher){
        $user = $this->getUser()->getUser();
        $footballer_repo = $manager->getRepository('App:Footballer');
        $conversation_repo = $manager->getRepository('App:Conversation');
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $private_message_repo = $manager->getRepository('App:PrivateMessage');
        $footballer = $footballer_repo->findOneByUser($user);
        $message = strip_tags($request->request->get('message'));
        $conversation_id = strip_tags($request->request->get('conversation'));
        $file = $request->files->get('image-chatroom');
        $date = new \DateTime('now');
        $path = '';
        if(strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev');
        $path .= $assetsManager->getUrl('/img/footballer/photo-profil/' .$this->getUser()->getId(). '/'.$footballer->getProfilPhoto());
        if(!is_null($message) && $message != '' && !is_null($conversation_id) && $conversation_id != ''){
            //Vérification si la conversation est bien associé au user
            $participation = $participant_conversations_repo->getMyParticipation($footballer, $conversation_id);
            $conversation = $conversation_repo->findOneById($conversation_id);
            if(!is_null($participation)){
                //Ajouter un message
                $private_message = new PrivateMessage();
                $private_message->setMessage($message);
                $private_message->setCreationDate($date);
                $private_message->setConversation($conversation);
                $private_message->setSender($footballer);
                $manager->persist($private_message);
                $manager->flush();

                $final_message['date'] = $date->format('d/m/Y H:i:s');
                $final_message['message'] = $message;
                $final_message['nom'] = $footballer->getUser()->getName().' '.$footballer->getUser()->getFirstName();
                $final_message['position'] = 'right';
                $final_message['conversation'] = $conversation_id;
                if(strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev');
                $path .= $assetsManager->getUrl('/img/footballer/photo-profil/' .$footballer->getUser()->getAccount()->getId(). '/'.$footballer->getProfilPhoto());
                $final_message['photo'] = '<img src="'.$path.'" alt="" class="profile-photo-sm pull-'.$final_message['position'].'"/>';
                $update = new Update('http://skillfoot.fr/users/private-message',
                    json_encode($final_message)
                );
                $publisher($update);
                return new JsonResponse($final_message);
            }

        }
//        else if(!is_null($file)){
//            $newFilename = $this->uploadFile($file, 'footballer_chatroom_directory', 300);
//            $chatroom_message = new ChatroomMessage();
//            $chatroom_message->setInternalLink($newFilename);
//            $chatroom_message->setCreationDate($date);
//            $chatroom_message->setSender($footballer);
//            $chatroom_message->setChatroomPeople($chatroom_repo->findOneByFootballer($footballer));
//            $manager->persist($chatroom_message);
//            $manager->flush();
//            $path_chatroom = '';
//            if(strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path_chatroom = $this->getParameter('url_dev');
//            $path_chatroom .= $assetsManager->getUrl('/img/footballer/chatroom/' .$this->getUser()->getId(). '/'.$newFilename);
//            $string = '<li class="left">
//                                <img src="'.$path.'" alt="" class="profile-photo-sm pull-left" />
//                                <div class="chat-item">
//                                    <div class="chat-item-header">
//                                        <h5>'.$user->getName().' '.$user->getFirstName().'</h5>
//                                        <small class="text-muted">'.$date->format('d/m/Y H:i:s').'</small>
//                                    </div>
//                                    <img src="'.$path_chatroom.'" width="300px" alt="" class="" />
//                                </div>
//                            </li>';
//            $update = new Update('http://skillfoot.fr/users/chat',
//                json_encode(['message' => $string])
//            );
//            $publisher($update);
//            return new JsonResponse(['result' => true]);
//        }
//        else{
//            return new JsonResponse(['result' => false]);
//        }
    }
    public function t($test){
        dump($test);
        die();
    }

    public function v($test){
        dump($test);
    }
}
