<?php


namespace Sequence\Provider;

use Psr\Log\InvalidArgumentException;
use Sequence\EventListener\RememberMeListener;
use Sequence\EventListener\SecurityListener;
use Sequence\ProviderInterface;
use Sequence\Container;
use Sequence\User\UserManager;
use Sequence\User\LoginManager;

class UserProvider implements ProviderInterface
{
    protected $optionsType = array(
        'user_manager' => array('verification', 'groups'),
        'login_manager' => array('last_active', 'multiple_session', 'verification', 'check_ip', 'firewall', 'login_url'),
    );

    public function register(Container $container, $options)
    {
        $options = isset($options['options']) ? $options['options'] : array();

        $userManager = new UserManager($container->get('database'),  $this->getOptions('user_manager', $options));
        $container->set('user_manager', $userManager);

        $loginManager = new LoginManager($container, $options);

        $firewallMap = array();

        if(!empty($options['firewall']))
        {
            if(empty($options['login_url']))
            {
                throw new InvalidArgumentException('Missing "login_url" route name.');
            } else
            {
                $options['login_url'] = $container->get('router')->generate($options['login_url']);
            }

            $firewallMap = require $container->get('config')->framework->get('app_dir').'/Resources/config/security.php';
        }

        $container->get('dispatcher')->addSubscriber(new SecurityListener($container, $userManager, $loginManager, $firewallMap, $this->getOptions('login_manager', $options)));
        $container->get('dispatcher')->addSubscriber(new RememberMeListener($loginManager, $container->get('database')));

        $container->set('user.login_manager', $loginManager);
    }

    /**
     * @param $type
     * @param $array
     * @return array
     */
    protected function getOptions($type, $array)
    {
        $options = array();

        foreach($array as $key => $val)
        {
            if(in_array($key, $this->optionsType[$type]))
            {
                $options[$key] = $val;
            }
        }

        return $options;
    }
} 