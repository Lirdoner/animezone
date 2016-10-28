<?php


namespace Anime\Controller;


use Anime\Model\Faq\FaqManager;
use Sequence\Controller;
use Sequence\User\Exception\ExceptionInterface;
use Sequence\User\LoginManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    protected $errorMessages = array(
        100 => 'Nieprawidłowy login i/lub hasło.',
        101 => 'Konto na które próbujesz się zalogować jest nieaktywne.',
        102 => 'Konto na które próbujesz się zalogować jest zbanowane.'
    );

    public function loginAction(Request $request)
    {
        $redirectUrl = $request->headers->get('referer', $this->generateUrl('homepage'));

        if($this->getUser()->isUser())
        {
            return $this->redirect($redirectUrl);
        }

        $login = $request->request->get('login');
        $password = $request->request->get('password');

        try
        {
            /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
            $response = $this->get('user.login_manager')->login($login, $password);

            //remember me
            if($request->request->get('rememberMe', false))
            {
                $response->headers->setCookie(new Cookie('rememberMe', $this->getSession()->getId(), new \DateTime('@'.strtotime('+30 days'))));
            }

            return $response;
        } catch(ExceptionInterface $e)
        {
            $this->getSession()->getFlashBag()->set('msg', array('danger' => $this->errorMessages[$e->getCode()]));
        }

        return $this->redirect($redirectUrl);
    }

    public function loginFacebookAction(Request $request)
    {
        /** @var \Sequence\Config $config */
        $config = $this->get('config');
        $session = $this->getSession();

        if(!$session->has('_user_redirect_url'))
        {
            $session->set('_user_redirect_url', $request->headers->get('referer', $this->generateUrl('homepage')));
        }

        require $config->framework->vendor_dir.'/Facebook/facebook.php';

        $facebook = new \Facebook(array(
            'appId'  => $config->anime->facebook->get('appId'),
            'secret' => $config->anime->facebook->get('secret'),
        ));

        $fbUser = $facebook->getUser();

        $redirectUrl = $facebook->getLoginUrl(array(
            'scope' => 'email, user_birthday, user_location',
            'redirect_uri' => $this->generateUrl('login_facebook', array(), true),
        ));

        if($request->query->get('error'))
        {
            $redirectUrl = $session->get('_user_redirect_url', $this->generateUrl('homepage'));
            $session->remove('_user_redirect_url');
        }

        if($fbUser)
        {
            try
            {
                // Proceed knowing you have a logged in user who's authenticated.
                $fbUserData = $facebook->api('/me');

                if(empty($fbUserData['email']))
                {
                    $session->getFlashBag()->set('msg', array(
                        'danger' => 'Wszystkie żądane uprawnienia są wymagane. Spróbuj zalogować się ponownie, nie odznaczając żadnych uprawnień.',
                    ));

                    return $this->redirect($this->generateUrl('homepage'));
                }

                /** @var \Sequence\User\UserManager $userManager */
                $userManager = $this->get('user_manager');

                //check if user exist
                if(false == $user = $userManager->findUserByEmail($fbUserData['email']))
                {
                    //add new user
                    $user = $userManager->createUser();
                    $user->setEnabled(1);
                    $user->setEmail($fbUserData['email']);
                    $user->setIp($request->server->get('REMOTE_ADDR'));
                    $user->setCustomField('gender', 'female' == $fbUserData['gender'] ? 2 : 1);
                    $user->setCustomField('facebook_id', $fbUserData['id']);

                    $nick = preg_replace('#[^\w-\.]#', '', $fbUserData['name']);
                    $nick = substr($nick, 0, 32);
                    if(false !== $userManager->findUserByUsername($nick) || strlen($nick) < 3)
                    {
                        $nick = $nick.'_'.substr(uniqid(), -3, 3);
                    }
                    $user->setUsername($nick);
                    $user->setPassword($fbUserData['email'].uniqid());

                    if(!empty($fbUserData['birthday']))
                    {
                        $birthday = new \DateTime($fbUserData['birthday']);
                        $user->getCustomField('birthday', $birthday->format('Y-m-d'));
                    }

                    $userManager->updateUser($user);

                    $session->getFlashBag()->set('msg', 'Twoje konto zostało utworzone. Automatycznie został wygenerowany twój login: <strong>'.$user->getUsername().'</strong>,
                        który możesz zmienić z poziomu <a href="'.$this->generateUrl('user_edit_profile').'">edycji profilu</a>.');
                } else
                {
                    //check if user is not blocked
                    if(2 == $user->getEnabled())
                    {
                        $session->getFlashBag()->set('msg', array(
                            'danger' => 'Twoje konto jest zablokowane. Skontakuj się z nami poprzez <a href="'.$this->generateUrl('contact').'">formularz kontaktowy</a> w celu wyjaśnienia wątpliwości.',
                        ));

                        return $this->redirect($this->generateUrl('homepage'));
                    }

                    //check if found user has facebook_id
                    if(0 == $user->getCustomField('facebook_id', 0))
                    {
                        //add facebok_id to existing user
                        $user->setCustomField('facebook_id', $fbUserData['id']);

                        //check if user is not activated
                        if(!$user->isEnabled())
                        {
                            $user->setEnabled(1);
                        }

                        $userManager->updateUser($user);

                        $session->getFlashBag()->set('msg', 'Twoje konto wraz z danymi zostało zintegrowane z kontem facebook.com.');
                    }
                }

                //login
                $response = $this->get('user.login_manager')->login($user->getEmail(), array('facebook_id' => $user->getCustomField('facebook_id')), LoginManager::TYPE_FACEBOOK);
                $response->headers->setCookie(new Cookie('rememberMe', $this->getSession()->getId(), new \DateTime('@'.strtotime('+30 days'))));

                return $response;
            } catch (\FacebookApiException $e)
            {
                $this->get('logger')->error($e->__toString());

                $session->getFlashBag()->set('msg', array(
                    'danger' => 'Wystąpił błąd podczas komunikacji z facebook.com. Prosimy spróbować ponownie się zalogować. W przypadku dalszych problemów,
                    prosimy o kontant poprzez <a href="'.$this->generateUrl('contact').'">formularz kontantowy</a>, lub użycie wbudowanego systemu logowania/rejestracji.',
                ));

                $redirectUrl = $this->generateUrl('homepage');
            }
        }

        return $this->redirect($redirectUrl);
    }

    public function logoutAction()
    {
        return $this->get('user.login_manager')->logout($this->generateUrl('homepage'));
    }

    public function loginViewAction(Request $request)
    {
        $redirectUrl = $request->headers->get('referer', $this->generateUrl('homepage'));

        if($this->getUser()->isUser())
        {
            return $this->redirect($redirectUrl);
        }

        if(!$this->getSession()->has('_user_redirect_url'))
        {
            $this->getSession()->set('_user_redirect_url', $request->headers->get('referer', $this->generateUrl('homepage')));
        }

        $faqManager = new FaqManager($this->getDatabase());

        return $this->render('Auth/login', array(
            'sidebar' => $faqManager->findAll(),
        ));
    }
} 