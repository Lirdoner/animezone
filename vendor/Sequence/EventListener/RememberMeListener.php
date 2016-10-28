<?php


namespace Sequence\EventListener;


use Sequence\Database\Database;
use Sequence\User\LoginManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RememberMeListener implements EventSubscriberInterface
{
    /** @var LoginManager  */
    private $loginManager;

    /** @var \Sequence\Database\Database  */
    private $database;

    public function __construct(LoginManager $loginManager, Database $database)
    {
        $this->loginManager = $loginManager;
        $this->database = $database;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $rememberMe = $request->cookies->get('rememberMe');

        if(!$request->hasSession() || null == $rememberMe)
        {
            return;
        }

        $session = $request->getSession();

        /** @var \Sequence\User\User $user */
        if(false === $user = $session->get('user', false))
        {
            return;
        }

        if($user->isUser())
        {
            return;
        }

        if(false == $userOnline = $this->database->select()->from('users_online')->where(array('sess_id' => $rememberMe))->get()->fetch())
        {
            return;
        }

        if($userOnline['user_id'])
        {
            $this->loginManager->login($userOnline['user_id'], array(), LoginManager::TYPE_ID);
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $session = $event->getRequest()->getSession();

        /** @var \Sequence\User\User $user */
        if(false == $user = $session->get('user', false))
        {
            return;
        }

        if($user->isUser())
        {
            $response->headers->setCookie(new Cookie('rememberMe', $session->getId(), new \DateTime('@'.strtotime('+30 days'))));
        } else
        {
            $response->headers->clearCookie('rememberMe');
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 14),
            KernelEvents::RESPONSE => 'onKernelResponse',
        );
    }
} 