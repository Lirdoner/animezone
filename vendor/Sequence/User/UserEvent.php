<?php


namespace Sequence\User;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class UserEvent extends Event
{
    /** @var  \Sequence\User\User */
    private $user;

    /** @var  \Symfony\Component\HttpFoundation\Request */
    private $request;

    /**
     * @param Request $request
     * @param User $user
     */
    public function __construct(Request $request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Sequence\User\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    public function getSession()
    {
        return $this->request->getSession();
    }
} 