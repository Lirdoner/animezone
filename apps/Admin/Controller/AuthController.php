<?php


namespace Admin\Controller;


use Sequence\Controller;
use Anime\Model\Watch\WatchManager;
use Anime\EventListener\UserListener;
use Symfony\Component\HttpFoundation\Request;
use Sequence\User\Exception\ExceptionInterface;

class AuthController extends Controller
{
    protected $errorMessages = array(
        100 => 'Nieprawidłowy login i/lub hasło.',
        101 => 'Konto na które próbujesz się zalogować jest nieaktywne.',
        102 => 'Konto na które próbujesz się zalogować jest zbanowane.'
    );

    public function init()
    {
        $this->get('dispatcher')->addSubscriber(new UserListener(new WatchManager($this->getDatabase())));
    }

    public function loginAction(Request $request)
    {
        if($this->getUser()->isUser())
        {
            return $this->redirect($this->generateUrl('dashboard'));
        }

        $msg = null;

        if($request->isMethod('post'))
        {
            $login = $request->request->get('login');
            $password = $request->request->get('password');

            try
            {
                /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
                $response = $this->get('user.login_manager')->login($login, $password);

                return $response;
            } catch(ExceptionInterface $e)
            {
                $msg = $this->errorMessages[$e->getCode()];
            }
        }

        return $this->render('Auth/login', array(
            'msg' => $msg,
        ));
    }

    public function logoutAction()
    {
        return $this->get('user.login_manager')->logout($this->generateUrl('dashboard'));
    }
} 