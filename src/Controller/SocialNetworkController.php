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

/** @Route("/footballer-social-network", name="social_network_") */
class SocialNetworkController extends AbstractController
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

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
                $blocked_list_of_footballer = $blocked_list_repo->getBlockedFootballer($footballer, $participant->getUser());
                if(is_null($blocked_list_of_footballer)){
                    $conversations[$participant->getConversation()->getId()]['participant'] = $participant;
                    $conversations[$participant->getConversation()->getId()]['conversation'] = $participant->getConversation();
                    $conversations[$participant->getConversation()->getId()]['notify'] = $participant->getNotify();
                    $conversations[$participant->getConversation()->getId()]['date'] = $participant->getModifiedAt()->format('Y-m-d H:i:s');
                }
            }
        }

        $dates = array_column($conversations, 'date');
        array_multisort($dates, SORT_DESC, $conversations);

        $user->setNotifyMessage(0);
        $manager->persist($user);
        $manager->flush();

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
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $private_message_repo = $manager->getRepository('App:PrivateMessage');
        $user = $this->getUser()->getUser();
        //Vérifier si j'y participe
        $participation = $participant_conversations_repo->getMyParticipation($user, $conversation_id);
        if(!is_null($participation)){
            //Récupération des messages
            $messages = $private_message_repo->findByConversation($conversation_id);
            //formation du retour : sender (left ou right), nom prénom et photo, message
            $final_message = [];
            foreach ($messages as $key => $message) {
                $date = $message->getCreationDate();
                $final_message[$key]['date'] = $date->format('d/m/Y H:i:s');
                $final_message[$key]['message'] = $message->getMessage();
                $final_message[$key]['nom'] = $message->getSender()->getName().' '.$message->getSender()->getFirstName();
                $final_message[$key]['position'] = ($message->getSender()->getId() == $user->getId() ? 'right' : 'left');
                $path = $this->getProfilPhoto($assetsManager, $message->getSender());
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
        $message = strip_tags($request->request->get('message'));
        $conversation_id = strip_tags($request->request->get('conversation'));
        $date = new \DateTime('now');
        if(!is_null($message) && $message != '' && !is_null($conversation_id) && $conversation_id != ''){
            //Vérification si la conversation est bien associé au user

            $participation = $participant_conversations_repo->getMyParticipation($user, $conversation_id);
            $conversation = $conversation_repo->findOneById($conversation_id);

            $participations = $participant_conversations_repo->findByConversation($conversation_id);

            foreach ($participations as $participation_item) {
                if($participation_item->getUser()->getId() != $user->getId()) {
                    $participation_item->getUser()->setNotifyMessage(1);
                    $manager->persist($participation_item);

                    $this->mail(
                        $participation_item->getUser()->getAccount()->getUsername(),
                        'Nouveau message de '.$user->getFirstName(),
                        'Nouveau message',
                        '
                <p style="font-size: 18px">Vous avez reçu un nouveau message de '.$user->getFirstName().'</p>
                <p style="font-size: 18px"><a style="color: white" href="https://skillfoot.fr/login">Cliquez-ici pour vous connecter.</a></p>
                '
                    );

                }
            }

            if(!is_null($participation)){
                //MAJ de la date de participation
                $participation->setModifiedAt((new \DateTime()));
                $participation->setNotify(1);
                $manager->persist($participation);
                $manager->persist($user);
                //Ajouter un message
                $private_message = new PrivateMessage();
                $private_message->setMessage($message);
                $private_message->setCreationDate($date);
                $private_message->setConversation($conversation);
                $private_message->setSender($user);
                $manager->persist($private_message);
                $manager->flush();

                $final_message['id'] = $user->getId();
                $final_message['date'] = $date->format('d/m/Y H:i:s');
                $final_message['message'] = $message;
                $final_message['nom'] = $user->getName().' '.$user->getFirstName();
                $final_message['conversation'] = $conversation_id;
                $path = $this->getProfilPhoto($assetsManager, $user);
                $final_message['photo'] = $path;
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
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $message = strip_tags($request->request->get('message'));
        $date = new \DateTime('now');

        if(!is_null($message) && $message){
            //Vérification si la footballeur est déjà en contact le footballeur concerné
            $participations = $participant_conversations_repo->searchParticipation($user, $footballer_target->getUser());
            if(empty($participations)){
                //CREATION D'UNE CONVERSATION
                $conversation = new Conversation();
                $conversation->setDatetime($date);
                $manager->persist($conversation);
                $manager->flush();
                //CREATION DES PARTICIPANTS DE LA CONVERSATION
                //PARTICIPANT 1
                $participation = new ParticipantConversation();
                $participation->setConversation($conversation);
                $participation->setUser($user);
                $participation->setModifiedAt((new \DateTime()));
                $participation->setParticipants(['['.$user->getId().']','['.$footballer_target->getUser()->getId().']']);
                $manager->persist($participation);
                $manager->flush();

                //PARTICIPANT 2
                $participation = new ParticipantConversation();
                $participation->setConversation($conversation);
                $participation->setUser($footballer_target->getUser());
                $participation->setModifiedAt((new \DateTime()));
                $participation->setParticipants(['['.$user->getId().']','['.$footballer_target->getUser()->getId().']']);
                $manager->persist($participation);
                $manager->flush();

                //AJOUT D'UN MESSAGE + REDIRECTION VERS UNE CONVERSATION EXISTANTE
                //Ajouter un message
                $private_message = new PrivateMessage();
                $private_message->setMessage($message);
                $private_message->setCreationDate($date);
                $private_message->setConversation($conversation);
                $private_message->setSender($user);
                $manager->persist($private_message);
                $manager->flush();

                //Transmission des info pour la nouvelle conversation
                $new_conversation = [
                    'conversation' => $conversation->getId(),
                    'nom' => $user->getName().' '.$user->getFirstName(),
                    'message' => $message,
                    'date' => $date->format('d/m/Y H:i:s'),
                    'photo' => $this->getProfilPhoto($assetsManager, $user)
                ];
                $update = new Update('http://skillfoot.fr/users/new-conversation',
                    json_encode($new_conversation)
                );
                $publisher($update);

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
                $private_message->setSender($user);
                $manager->persist($private_message);
                $manager->flush();
            }

            //redirection vers la conversation
            if(in_array('ROLE_AGENT', $this->getUser()->getRoles())){
                return $this->redirectToRoute('agent_messages',['id' => $conversation->getId()]);
            }else{
                return $this->redirectToRoute('social_network_show_conversations',['id' => $conversation->getId()]);
            }
        }
    }

    /**
     * @Route("/remove-notify", name="remove_notify", methods={"POST"})
     */
    public function removeNotify(Request $request, EntityManagerInterface $manager, \Symfony\Component\Asset\Packages $assetsManager, PublisherInterface $publisher){
        $user = $this->getUser()->getUser();
        $participant_conversations_repo = $manager->getRepository('App:ParticipantConversation');
        $params = $request->request->all();
        $participation = $participant_conversations_repo->findOneById($params['participant']);
        $conversation = $participation->getConversation();
        $all_participation = $participant_conversations_repo->findByConversation($conversation);

        foreach ($all_participation as $item) {
            if($item->getUser()->getId() == $user->getId()){
                $participation->setNotify(0);
                $manager->persist($participation);
                $manager->flush();
                return new JsonResponse(['result' => true]);
            }
        }

        $user->setNotifyMessage(0);
        $manager->persist($user);
        $manager->flush();

        return new JsonResponse(['result' => false]);

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
                $path .= $assetsManager->getUrl('/img/default/profil-footballer.png');
            }
        }else{
            if(!is_null($user->getProfilPhoto())) {
                $path .= $assetsManager->getUrl('/img/user/photo-profil/' . $user->getAccount()->getId() . '/' . $user->getProfilPhoto());
            }
            else{
                $path .= $assetsManager->getUrl('/img/default/profil-agent.png');
            }
        }
        return $path;
    }

    function mail($mail, $objet, $titre, $contain)
    {
        $message = (new \Swift_Message())
            ->setFrom('noreply@hskillfoot.fr')
            ->setTo($mail)
            ->setSubject($objet);

        $img = $message->embed(\Swift_Image::fromPath('img/logo/logo.png'));
        $message->setBody(
            $this->renderView(
            // templates/emails/registration.html.twig
                'mail.html.twig',
                [
                    'img' => $img,
                    'titre' => $titre,
                    'message' => $contain,
                ]
            ),
            'text/html'
        );

        $this->mailer->send($message);
    }

    public function t($test){
        dump($test);
        die();
    }

    public function v($test){
        dump($test);
    }
}
