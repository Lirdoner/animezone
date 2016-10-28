<?php


namespace Anime\EventListener;


use Anime\Model\Watch\WatchBag;
use Anime\Model\Watch\WatchManager;
use Sequence\User\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserListener implements EventSubscriberInterface
{
    /** @var  \Anime\Model\Watch\WatchManager */
    protected $watchManager;

    /**
     * @param WatchManager $manager
     */
    public function __construct(WatchManager $manager)
    {
        $this->watchManager = $manager;
    }

    /**
     * @param UserEvent $event
     */
    public function onLogin(UserEvent $event)
    {
        $session = $event->getSession();

        if(!$session->get('watched'))
        {
            $bag = array();

            foreach($this->watchManager->findBy(array('user_id' => $event->getUser()->getId())) as $row)
            {
                $bag[$row['category_id']] = $row['type'];
            }

            $session->set('watched', new WatchBag($bag));
        }
    }

    /**
     * @param UserEvent $event
     */
    public function onLogout(UserEvent $event)
    {
        $event->getSession()->remove('watched');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'user.login' => 'onLogin',
            'user.logout' => 'onLogout',
        );
    }
} 