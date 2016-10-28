<?php


namespace Sequence\User;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Sequence\Container;
use Sequence\User\Exception\UserNotFoundException;
use Sequence\User\Exception\UserNotVerifiedException;
use Sequence\User\Exception\UserBannedException;

class LoginManager
{
    const TYPE_PASSWORD = 'password';
    const TYPE_FACEBOOK = 'facebook';
    const TYPE_ID = 'ID';

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @var \Sequence\User\UserManager
     */
    protected $userManager;

    /**
     * @var \Sequence\Database\Database
     */
    protected $database;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var \Sequence\Container
     */
    protected $container;


    public function __construct(Container $container, array $options = null)
    {
        $this->userManager = $container->get('user_manager');
        $this->request = $container->get('request');
        $this->session = $this->request->getSession();
        $this->database = $container->get('database');
        $this->dispatcher = $container->get('dispatcher');
        $this->container = $container;

        $this->options = array_merge(array(
            'last_active' => 150,
            'verification' => 1,
            'check_ip' => false,
            'multiple_session' => false
        ), $options);
    }

    /**
     * @param string $usernameOrEmail
     * @param string|array $passwordOrCustom
     * @param string $type
     *
     * @return RedirectResponse
     *
     * @throws UserNotFoundException
     * @throws UserNotVerifiedException
     * @throws UserBannedException
     */
    public function login($usernameOrEmail, $passwordOrCustom, $type = self::TYPE_PASSWORD)
    {
        $loginData = array();

        if(is_array($passwordOrCustom))
        {
            foreach($passwordOrCustom as $column => $value)
            {
                $loginData[$column] = $value;
            }
        }

        if($type == self::TYPE_PASSWORD || $type == self::TYPE_FACEBOOK)
        {
            if(filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL))
            {
                $loginData['email'] = $usernameOrEmail;
            } else
            {
                $loginData['name'] = $usernameOrEmail;
            }
        } elseif($type == self::TYPE_ID)
        {
            $loginData['id'] = $usernameOrEmail;
        }

        if(!$this->session->has('_user_redirect_url'))
        {
            $this->session->set('_user_redirect_url', $this->request->headers->get('referer', $this->request->getBaseUrl()));
        }

        if(false !== $user = $this->userManager->findUserBy($loginData))
        {
            if(self::TYPE_PASSWORD == $type && !password_verify($passwordOrCustom, $user->getPassword()))
            {
                throw new UserNotFoundException('Incorrect password.', 100);
            } else if(0 == $user->getEnabled())
            {
                throw new UserNotVerifiedException('Your account is not yet active.', 101);
            } else if(3 == $user->getEnabled())
            {
                throw new UserBannedException('Your account is banned.', 102);
            }

            $user->setIp($this->request->server->get('REMOTE_ADDR'));
            $user->setLastLogin(new \DateTime());

            if(!$this->options['multiple_session'])
            {
                $this->database->delete('users_online')->where(array('user_id' => $user->getId()))->get();
            }

            $this->updateOnline(array('user_id' => $user->getId(), 'user_role' => $user->getRole()));

            $this->userManager->updateUser($user);

            $this->session->set('user', $user);
            $this->session->set('_user_last_active', time() + $this->options['last_active']);

            $url = $this->session->get('_user_redirect_url');
            $this->session->remove('_user_redirect_url');

            $this->container->set('user', $user);


            $this->dispatcher->dispatch('user.login', new UserEvent($this->request, $user));

            return new RedirectResponse($url ?: '/');
        }

        throw new UserNotFoundException('Incorrect login and/or email.', 100);
    }

    /**
     * @param string $defaultUrl
     *
     * @return RedirectResponse
     */
    public function logout($defaultUrl)
    {
        $redirectResponse = new RedirectResponse($defaultUrl);

        if($this->session->has('user'))
        {
            $redirectResponse->setTargetUrl($this->session->get('_user_redirect_url', $this->request->headers->get('referer', $defaultUrl)));
            $redirectResponse->headers->clearCookie('rememberMe');

            $this->session->remove('_user_redirect_url');

            $user = $this->createGuest();
            $this->session->set('user', $user);
            $this->session->set('_user_last_active', time() + $this->options['last_active']);
            $this->database->delete('users_online')->where(array('sess_id' => $this->session->getId()))->get();

            $this->dispatcher->dispatch('user.logout', new UserEvent($this->request, $user));
        }

        return $redirectResponse;
    }

    /**
     * @param array $data
     */
    public function updateOnline(array $data = null)
    {
        $data = array_merge(array(
            'sess_id' => $this->session->getId(),
            'user_ip' => $this->request->server->get('REMOTE_ADDR'),
            'user_agent' => $this->request->headers->get('User-Agent'),
        ), $data);

        if($this->database->select()->from('users_online')->where(array('sess_id' => $this->session->getId()))->get()->rowCount())
        {
            $this->database->update('users_online', $data)->where(array('sess_id' => $data['sess_id']))->get();
        } else
        {
            $this->database->insert('users_online', $data)->get();
        }
    }

    /**
     * @return User
     */
    public function createGuest()
    {
        $user = new User(array('ip' => $this->request->server->get('REMOTE_ADDR')));
        $this->session->set('user', $user);

        return $user;
    }
} 