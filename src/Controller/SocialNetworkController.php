<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Footballer;
use App\Entity\ParticipantConversation;
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
     * @Route("/show-conversations/{id}", name="show_conversations", defaults={"id"=0})
     */
    public function showConversation($id, Request $request, EntityManagerInterface $manager, PublisherInterface $publisher, \Symfony\Component\Asset\Packages $assetsManager)
    {
        $footballer_repo = $manager->getRepository('App:Footballer');
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $blocked_list_repo = $manager->getRepository('App:BlockFriendsList');
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
                $blocked_list_of_footballer = $blocked_list_repo->getBlockedFootballer($footballer, $participant->getFootballer());
                if(is_null($blocked_list_of_footballer)){
                    $conversations[$participant->getConversation()->getId()]['participant'] = $participant->getFootballer();
                    $conversations[$participant->getConversation()->getId()]['conversation'] = $participant->getConversation();
                }
            }
        }
        return $this->render('socialNetwork/newsfeed/show-conversation.html.twig',[
            'conversations' => $conversations,
            'id' => $id
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
                if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
                    isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev'); $path = $this->getParameter('url_dev');
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
    public function addMessage(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager, PublisherInterface $publisher){
        $user = $this->getUser()->getUser();
        $footballer_repo = $manager->getRepository('App:Footballer');
        $conversation_repo = $manager->getRepository('App:Conversation');
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $footballer = $footballer_repo->findOneByUser($user);
        $message = strip_tags($request->request->get('message'));
        $conversation_id = strip_tags($request->request->get('conversation'));
        $file = $request->files->get('image-chatroom');
        $date = new \DateTime('now');
        $path = '';
        if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
            isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev'); $path = $this->getParameter('url_dev');
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
                if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
                    isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev'); $path = $this->getParameter('url_dev');
                $path .= $assetsManager->getUrl('/img/footballer/photo-profil/' .$footballer->getUser()->getAccount()->getId(). '/'.$footballer->getProfilPhoto());
                $final_message['photo'] = '<img src="'.$path.'" alt="" class="profile-photo-sm pull-'.$final_message['position'].'"/>';
                $update = new Update('http://skillfoot.fr/users/private-message',
                    json_encode($final_message)
                );
                $publisher($update);
                return new JsonResponse($final_message);
            }
        }
    }

    /**
     * @Route("/send-message/{id}", name="send_message", methods={"POST"})
     */
    public function sendMessage(Footballer $footballer_target, Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager, PublisherInterface $publisher){
        $user = $this->getUser()->getUser();
        $footballer_repo = $manager->getRepository('App:Footballer');
        $conversation_repo = $manager->getRepository('App:Conversation');
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $footballer = $footballer_repo->findOneByUser($user);
        $message = strip_tags($request->request->get('message'));
        $date = new \DateTime('now');
        $path = '';
        if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
            isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false) $path = $this->getParameter('url_dev'); $path = $this->getParameter('url_dev');
        $path .= $assetsManager->getUrl('/img/footballer/photo-profil/' .$this->getUser()->getId(). '/'.$footballer->getProfilPhoto());

        if(!is_null($message) && $message){
            //Vérification si la footballeur est déjà en contact le footballeur concerné
            $participations = $participant_conversations_repo->searchParticipation($footballer, $footballer_target);
            if(empty($participations)){
                //CREATION D'UNE CONVERSATION
                $conversation = new Conversation();
                $conversation->setDatetime($date);
                $manager->persist($conversation);
                $manager->flush();
                //CREATION DES PARTICIPANTS DE LA CONVERSATION
                $participation = new ParticipantConversation();
                $participation->setConversation($conversation);
                $participation->setFootballer($footballer);
                $participation->setParticipants(['['.$footballer->getId().']','['.$footballer_target->getId().']']);
                $manager->persist($participation);
                $manager->flush();

                $participation = new ParticipantConversation();
                $participation->setConversation($conversation);
                $participation->setFootballer($footballer_target);
                $participation->setParticipants(['['.$footballer->getId().']','['.$footballer_target->getId().']']);
                $manager->persist($participation);
                $manager->flush();
                //AJOUT D'UN MESSAGE + REDIRECTION VERS UNE CONVERSATION EXISTANTE
                //Ajouter un message
                $private_message = new PrivateMessage();
                $private_message->setMessage($message);
                $private_message->setCreationDate($date);
                $private_message->setConversation($conversation);
                $private_message->setSender($footballer);
                $manager->persist($private_message);
                $manager->flush();

            }else{
                $final_participation = null;
                foreach ($participations as $participation) {
                    if(count($participation->getParticipants()) == 2){
                        $final_participation = $participation;
                    }
                }

                $conversation = $final_participation->getConversation();

                //Ajout du message
                $private_message = new PrivateMessage();
                $private_message->setMessage($message);
                $private_message->setCreationDate($date);
                $private_message->setConversation($conversation);
                $private_message->setSender($footballer);
                $manager->persist($private_message);
                $manager->flush();
            }
            $update = new Update('http://skillfoot.fr/users/private-message',
                json_encode($message)
            );
            $publisher($update);

            //redirection vers la conversation
            return $this->redirectToRoute('footballer_social_network_show_conversations',['id' => $conversation->getId()]);
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
