<?php


namespace Sequence\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sequence\Container;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Sequence\User\Exception\AccessDeniedException;
use Sequence\User\LoginManager;
use Sequence\User\UserManager;


class SecurityListener implements EventSubscriberInterface
{
    /**
     * @var \Sequence\Container
     */
    protected $container;

    /**
     * @var \Sequence\User\UserManager
     */
    protected $userManager;

    /**
     * @var \Sequence\User\LoginManager
     */
    protected $loginManager;

    /**
     * @var \Sequence\Database\Database
     */
    protected $database;

    protected $options;
    protected $firewallMap;

    /**
     * @param Container $container
     * @param UserManager $userManager
     * @param LoginManager $loginManager
     * @param array $firewallMap
     * @param array $options
     */
    public function __construct(Container $container, UserManager $userManager, LoginManager $loginManager, array $firewallMap, array $options = null)
    {
        $this->container = $container;
        $this->userManager = $userManager;
        $this->loginManager = $loginManager;
        $this->database = $container->get('database');

        $this->firewallMap = $firewallMap;
        $this->options = array_merge(array(
            'last_active' => 120,
            'verification' => 1,
            'check_ip' => false,
            'multiple_session' => false
        ), $options);
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onAuthentication(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if(!$request->hasSession())
        {
            return;
        }

        $session = $request->getSession();

        if(false === $user = $session->get('user', false))
        {
            $user = $this->loginManager->createGuest();
        }

        if($this->options['check_ip'] && $request->server->get('REMOTE_ADDR') !== $user->getIp())
        {
            $user = $this->loginManager->createGuest();
        }

        if($session->get('_user_last_active', 0) < time() && $user->isUser())
        {
            $now = new \DateTime();

            if(false == $this->database->select()->from('users_online')->where(array('user_id' => $user->getId()))->get()->fetch())
            {
                $user = $this->loginManager->createGuest();
            } else
            {
                $user->setLastLogin($now);
                $this->userManager->updateLastLogin($user);

                $session->set('user', $user);
            }

            $session->set('_user_last_active', time() + $this->options['last_active']);

            if($user->isUser())
            {
                $this->loginManager->updateOnline(array(
                    'user_id' => $user->getId(),
                    'user_role' => $user->getRole(),
                    'last_active' => $now->format('Y-m-d H:i:s')
                ));
            }
        }

        $this->container->set('user', $user);
    }

    /**
     * @param GetResponseEvent $event
     * @throws \Sequence\User\Exception\AccessDeniedException
     */
    public function onAuthorization(GetResponseEvent $event)
    {
        if(empty($this->options['firewall']) || empty($this->options['login_url']))
        {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        $user = $session->get('user');
        $route = $request->attributes->get('_route');

        if(isset($this->firewallMap[$route]))
        {
            if(false === $user->hasRole($this->firewallMap[$route]))
            {
                if(!$user->isUser())
                {
                    $session->set('_user_redirect_url', $request->getUri());

                    $event->setResponse(new RedirectResponse($this->options['login_url']));
                } else
                {
                    throw new AccessDeniedException();
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(
                array('onAuthentication', 16),
                array('onAuthorization', 8),
            ),
        );
    }
} 