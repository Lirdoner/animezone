<?php


namespace Anime\Controller;


use Anime\EventListener\UserListener;
use Anime\Model\Comment\CommentManager;
use Anime\Model\Faq\FaqManager;
use Anime\Model\Favorite\FavoriteManager;
use Anime\Model\Rating\RatingManager;
use Anime\Model\Restore\RestoreManager;
use Anime\Model\Watch\Watch;
use Anime\Model\Watch\WatchManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Sequence\Util\SimpleImage;
use Sequence\Validator\Date;
use Sequence\Validator\StringLength;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /** @var \Anime\Model\Faq\FaqManager  */
    protected $faqManager;

    protected $msg;

    public function init()
    {
        $this->faqManager = new FaqManager($this->getDatabase());
    }

    public function profileAction($user_name, $action)
    {
        /** @var \Sequence\User\UserManager $userManager */
        $userManager = $this->get('user_manager');

        $tabs = array(
            'favorites' => 'Ulubione',
            'rated' => 'Oceniłem',
            'watching' => 'Oglądam',
            'plans' => 'Planuje',
        );

        if(false == $user = $userManager->findUserByUsername($user_name))
        {
            throw $this->createNotFoundException(sprintf('Użytkownik o podanym loginie: "%s" nie istnieje.', $user_name));
        }

        $commentManager = new CommentManager($this->getDatabase());
        $comments = $commentManager->findForUser($user->getId(), 'date DESC', 10)->get();

        if('favorites' == $action)
        {
            $favoriteManager = new FavoriteManager($this->getDatabase());
            $data = $favoriteManager->findForUser($user->getId(), 'date DESC', 6);
        } elseif('rated' == $action)
        {
            $ratingManager = new RatingManager($this->getDatabase());
            $data = $ratingManager->findForUser($user->getId(), 'date DESC', 6);
        } else //watching
        {
            $watchManager = new WatchManager($this->getDatabase());

            if('plans' == $action)
            {
                $data = $watchManager->findForUser($user->getId(), 'date DESC', 6, Watch::PLANS);
            } elseif('watching' == $action)
            {
                $data = $watchManager->findForUser($user->getId(), 'date DESC', 6, Watch::WATCHING);
            } elseif('watched' == $action)
            {
                $data = $watchManager->findForUser($user->getId(), 'date DESC', 6, Watch::WATCHED);
            } elseif('stopped' == $action)
            {
                $data = $watchManager->findForUser($user->getId(), 'date DESC', 6, Watch::STOPPED);
            } else //abandoned
            {
                $data = $watchManager->findForUser($user->getId(), 'date DESC', 6, Watch::ABANDONED);
            }
        }

        return $this->render('User/profile', array(
            'user' => $user,
            'action' => $action,
            'tabs' => $tabs,
            'data' => $data->get(),
            'comments' => $comments,
            'sidebar' => $this->faqManager->findAll(),
        ));
    }

    public function profileDetailsAction($user_name, $action, Request $request)
    {
        /** @var \Sequence\User\UserManager $userManager */
        $userManager = $this->get('user_manager');

        $tabs = array(
            'favorites' => 'Ulubionych',
            'rated' => 'Ocenionych',
            'commented' => 'Komentarzy',
            'watching' => 'Oglądanych',
            'watched' => 'Obejrzanych',
            'plans' => 'Planowanych',
            'stopped' => 'Wstrzymanych',
            'abandoned' => 'Porzuconych',
        );

        if(false == $user = $userManager->findUserByUsername($user_name))
        {
            throw $this->createNotFoundException(sprintf('Użytkownik o podanym loginie: "%s" nie istnieje.', $user_name));
        }

        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('user_profile_details', array('user_name' => $user->getUsername(), 'action' => $action)))->
            setUrl($this->generateUrl('user_profile_details', array('user_name' => $user->getUsername(), 'action' => $action, 'page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_');

        if('favorites' == $action)
        {
            $favorites = new FavoriteManager($this->getDatabase());
            $list = $favorites->findForUser($user->getId(), 'date DESC');
        } elseif('rated' == $action)
        {
            $ratings = new RatingManager($this->getDatabase());
            $list = $ratings->findForUser($user->getId(), 'date DESC');
        } elseif('commented' == $action)
        {
            $comments = new CommentManager($this->getDatabase());
            $list = $comments->findForUser($user->getId(), 'date DESC');
        } else
        {
            $watch = new Watch();
            $constants = (new \ReflectionClass($watch))->getConstants();

            $watching = new WatchManager($this->getDatabase());
            $list = $watching->findForUser($user->getId(), 'date DESC', null, $constants[strtoupper($action)]);
        }

        try
        {
            $pagination->
                setTotalCount($list->get()->rowCount())->
                setCurrentPage($request->query->get('page', 1));
        } catch(\InvalidArgumentException $e)
        {
            throw $this->createNotFoundException();
        }

        $list = $list->offset($pagination->offset())->limit($pagination->limit());

        return $this->render('User/profile_details', array(
            'user' => $user,
            'action' => $action,
            'tabs' => $tabs,
            'list' => $list->get(),
            'pagination' => $pagination->getHtml('margin:0', 'pagination-lg'),
        ));
    }

    public function editAction(Request $request)
    {
        /** @var \Sequence\User\UserManager $userManager */
        $userManager = $this->get('user_manager');
        /** @var \Sequence\User\User $user */
        $user = $this->getUser();
        $error = array();
        $changes = false;
        $session = $this->getSession();

        if($facebook = $user->getCustomField('facebook_id', 0))
        {
            $ch = curl_init('http://graph.facebook.com/'.$facebook.'/picture?redirect=false&width=100&height=100');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            if($json = curl_exec($ch))
            {
                $facebook = json_decode($json);
                $facebook = empty($facebook->data->url) ? false : $facebook->data->url;
            } else
            {
                $facebook = false;
            }

            curl_close($ch);
        }

        if($request->isMethod('post'))
        {
            //change avatar
            if(false !== $source = $request->request->get('source', false))
            {
                if('default' == $source)
                {
                    $oldAvatar = $user->getCustomField('avatar');
                    if(!strstr($oldAvatar, 'http://') && $oldAvatar)
                    {
                        $fileSystem = new Filesystem();
                        $fileSystem->remove($this->get('config')->anime->get('avatars_dir').$oldAvatar);
                    }

                    $user->setCustomField('avatar', null);
                    $session->getFlashBag()->set('msg', 'Twój avatar został zmieniony na domyślny.');
                    $changes = true;
                } elseif('gravatar' == $source)
                {
                    $user->setCustomField('avatar', 'http://www.gravatar.com/avatar/'.md5($user->getEmail()).'.jpg?s=100');
                    $session->getFlashBag()->set('msg', 'Twój avatar został zmieniony na zdjęcie z <strong>Gravatar.com</strong>.');
                    $changes = true;
                } elseif('facebook' == $source && $facebook)
                {
                    $user->setCustomField('avatar', $facebook);
                    $session->getFlashBag()->set('msg', 'Twój avatar został zmieniony na zdjęcie z <strong>Facebook.com</strong>.');
                    $changes = true;
                } elseif('computer' == $source && $file = $request->files->get('picture_file', false))
                {
                    $image = new SimpleImage();

                    try
                    {
                        $image->load($file->getPathname());
                    } catch(\Exception $e)
                    {
                        $error[] = 'Nieobsługiwany format pliku. Jedynie jpg, png lub gif';
                    }

                    if($image->get_height() > 100 || $image->get_width() > 100)
                    {
                        $error[] = 'Nieprawidłowy rozmiar obrazu. Maksymalny rozmiar to 100x100 pikseli.';
                    }

                    if($file->getSize() > 102400)
                    {
                        $error[] = 'Rozmiar pliku jest za duży. Maksymalny rozmiar to 100kb.';
                    }

                    if(empty($error))
                    {
                        $name = $image->get_original_info();
                        $name = md5($user->getEmail().uniqid()).'.'.$name['format'];
                        $path = $this->get('config')->anime->get('avatars_dir');

                        try
                        {
                            $image->resize(100, 100);
                            $image->save($path.$name, 90);
                        } catch(\Exception $e)
                        {
                            $error[] = 'Nieobsługiwany format pliku. '.$e->getMessage();
                        }

                        if(empty($error))
                        {
                            $user->setCustomField('avatar', $name);
                            $session->getFlashBag()->add('msg', 'Twój avatar został zmieniony na zdjęcie z komputera.');
                            $changes = 1;
                        }
                    }
                }
            }

            //change email
            $email = $request->request->get('email', false);
            if($email !== $user->getEmail() && false !== $email)
            {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $error[] = 'Wprowadzony adres e-mail jest niepoprawny.';
                }

                if($userManager->findUserByEmail($email))
                {
                    $error[] = 'Wprowadzony adres e-mail jest przypisany do innego użytkownika.';
                }

                if(empty($error))
                {
                    $user->setEmail($email);
                    $session->getFlashBag()->add('msg', 'Twój adres e-mail został zmieniony.');
                    $changes = true;
                }
            }

            //change username
            $username = $request->request->get('name', false);
            if($username !== $user->getUsername() && false !== $username)
            {
                $loginLength = new StringLength(array('min' => 3, 'max' => '32'));
                if(!$loginLength->isValid($username))
                {
                    $error[] = 'Login jest za krótki (minimum 3 znaki), lub za długi (maksimum 32).';
                }

                if(preg_match('#[^\w-\.]+#', $username))
                {
                    $error[] = 'Login jest niepoprawny. Tylko litery (duże lub małe), cyfry, myślniki, kropki oraz podkreślniki (underscore _ ).';
                }

                if(preg_match('#register|restore|edit#i', $username))
                {
                    $error[] = 'Nie możesz użyć tego typu loginu, ponieważ jest on częścia adresu url.
                    Podobne zabronione wyrazy to: <strong>register</strong>, <strong>restore</strong>, <strong>edit</strong>.';
                }

                if(false !== $userManager->findUserByUsername($username))
                {
                    $error[] = 'Użytkownik o podanym loginie już istnieje w naszym systemie.';
                }

                if(empty($error))
                {
                    $user->setUsername($username);
                    $session->getFlashBag()->add('msg', 'Twój login został zmieniony.');
                    $changes = true;
                }
            }

            //change location
            $location = $request->request->get('location', false);
            if($location !== $user->getCustomField('location') && false !== $location)
            {
                $location = strip_tags($location);
                $validLocation = new StringLength(array('min' => 3, 'max' => 30));
                if(!$validLocation->isValid($location))
                {
                    $error[] = 'Lokalizacja jest nieprawidłowa. Minimum 3 znaki, maksimum 30.';
                }

                if(empty($error))
                {
                    $user->setCustomField('location', $location);
                    $session->getFlashBag()->add('msg', 'Miejscowość została zmieniona.');
                    $changes = true;
                }
            }

            //check birthdate
            $birthdate = $request->request->get('birthdate', false);
            if($birthdate !== $user->getCustomField('birthdate') && false !== $birthdate)
            {
                $validDate = new Date(array('format' => 'Y-m-d'));
                if(!$validDate->isValid($birthdate))
                {
                    $error[] = 'Format daty urodzenia jest niepoprawny.';
                }

                if(empty($error))
                {
                    $user->setCustomField('birthdate', $birthdate);
                    $session->getFlashBag()->add('msg', 'Data urodzenia została zmieniona została zmieniona.');
                    $changes = true;
                }
            }

            //check passwords
            $passwords = $request->request->get('password', false);
            if(isset($passwords['current']) && isset($passwords['new']) && isset($passwords['new2']))
            {
                $user->setPassword($passwords['current']);

                $checkPassword = $userManager->findUserBy(array('id' => $user->getId()));
                if(!password_verify($passwords['current'], $checkPassword->getPassword()))
                {
                    $error[] = 'Aktualne hasło jest nieprawidłowe.';
                }

                if($passwords['current'] == $passwords['new'])
                {
                    $error[] = 'Nowe hasło musi być różne od aktualnego.';
                }

                $validPassword = new StringLength(array('min' => 5, 'max' => 32));
                if(!$validPassword->isValid($passwords['new']))
                {
                    $error[] = 'Nowe hasło jest niepoprawne. Minimum 5 znaków, maksimum 32.';
                }

                if($passwords['new'] !== $passwords['new2'])
                {
                    $error[] = 'Nowe hasło i powtórzenie nowego hasła muszą być identyczne.';
                }

                if(empty($error))
                {
                    $user->setPassword($passwords['new']);
                    $session->getFlashBag()->add('msg', 'Hasło zostało zmienione.');
                    $changes = true;
                }
            }

            //update changes
            if(empty($error) && $changes)
            {
                $userManager->updateUser($user);
                $session->set('user', $user);
            }
        }

        return $this->render('User/edit', array(
            'user' => $user,
            'error' => $error,
            'facebook' => $facebook,
            'sidebar' => $this->faqManager->findAll(),
        ));
    }

    public function registerAction(Request $request)
    {
        $error = array();
        /** @var \Sequence\User\UserManager $userManager */
        $userManager = $this->get('user_manager');
        $user = $userManager->createUser();

        if($this->getUser()->isUser())
        {
            return $this->redirect($this->generateUrl('homepage'));
        }

        if($request->isMethod('post'))
        {
            $required = array('name', 'password', 'password2', 'email', 'location', 'birthdate', 'gender');
            $data = $request->request->get('user', array());

            //check if there is less or more than expected
            $diff = array_diff($required, array_keys($data));
            if(!empty($diff))
            {
                $error[] = 'Przesłane dane są niepoprawne lub niekompletne.';
            }

            //check code
            if(strcmp($request->request->get('code'), $this->getSession()->get('captcha')))
            {
                $error[] = 'Przepisany kod jest niepoprawny.';
            } else
            {
                $this->getSession()->remove('captcha');
            }

            //check username
            $user->setUsername($data['name']);

            $loginLength = new StringLength(array('min' => 3, 'max' => '32'));
            if(!$loginLength->isValid($user->getUsername()))
            {
                $error[] = 'Login jest za krótki (minimum 3 znaki), lub za długi (maksimum 32).';
            }

            if(preg_match('#[^\w-\.]+#', $user->getUsername()))
            {
                $error[] = 'Login jest niepoprawny. Tylko litery (duże lub małe), cyfry, myślniki, kropki oraz podkreślniki (underscore _ ).';
            }

            if(preg_match('#register|restore|edit#i', $user->getUsername()))
            {
                $error[] = 'Nie możesz użyć tego typu loginu, ponieważ jest on częścia adresu url.
                Podobne zabronione wyrazy to: <strong>register</strong>, <strong>restore</strong>, <strong>edit</strong>.';
            }

            if(false !== $userManager->findUserByUsername($user->getUsername()))
            {
                $error[] = 'Użytkownik o podanym loginie już istnieje w naszym systemie.';
            }

            //check password
            $validPassword = new StringLength(array('min' => 5, 'max' => 32));
            if($data['password'] == $data['password2'] && $validPassword->isValid($data['password']))
            {
                $user->setPassword($data['password']);
            } else
            {
                $error[] = 'Hasło jest niepoprawne. Minimum 5 znaków, maksimum 32. Oba hasła muszą być identyczne.';
            }

            //check email
            $user->setEmail($data['email']);
            if(!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL))
            {
                $error[] = 'Email jest niepoprawny.';
            }
            $user->setEmail($data['email']);

            if(false !== $userManager->findUserByEmail($user->getEmail()))
            {
                $error[] = 'Użytkownik o podanym adresie e-mail już istnieje w naszym systemie.';
            }

            //check location
            $validLocation = new StringLength(array('min' => 3, 'max' => 30));
            $data['location'] = strip_tags($data['location']);
            if(!$validLocation->isValid($data['location']))
            {
                $error[] = 'Lokalizacja jest nieprawidłowa. Minimum 3 znaki, maksimum 30.';
            }
            $user->setCustomField('location', $data['location']);

            //check birth date
            $validDate = new Date(array('format' => 'Y-m-d'));
            if(!$validDate->isValid($data['birthdate']))
            {
                $error[] = 'Format daty urodzenia jest niepoprawny.';
            }
            $user->setCustomField('birthdate', $data['birthdate']);

            //check gender
            $data['gender'] = empty($data['gender']) ? 0 : $data['gender'];
            if(!in_array($data['gender'], range(1, 2)))
            {
                $error[] = 'Płeć jest niepoprawna.';
            }
            $user->setCustomField('gender', $data['gender']);

            if(empty($error))
            {
                $user->setEnabled(0);
                $user->setIp($request->server->get('REMOTE_ADDR'));
                $userManager->updateUser($user);

                $id = hash('sha256', $user->getEmail().$user->getPassword());

                //send email with activation code
                /** @var \Sequence\Mail\Mailer $mail */
                $mail = $this->get('mailer');
                $mail->addAddress($user->getEmail());
                $mail->Subject = $title ='Potwierdzenie rejstracji na AnimeZone.pl';
                $mail->Body = $this->renderView('Mail/User/register', array(
                    'title' => $title,
                    'login' => $user->getUsername(),
                    'code' => $id,
                    'email' => urlencode($user->getEmail()),
                ));
                $mail->send();

                $this->getSession()->getFlashBag()->set('msg', 'Na podany przez ciebie adres e-mail został przesłany list aktywacyjny.');

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render('User/register', array(
            'user' => $user,
            'error' => $error,
            'sidebar' => $this->faqManager->findAll(),
        ));
    }

    public function registerConfirmAction($code, $email)
    {
        /** @var \Sequence\User\UserManager $userManager */
        $userManager = $this->get('user_manager');

        if($this->getUser()->isUser())
        {
            return $this->redirect($this->generateUrl('homepage'));
        }

        if(false == $user = $userManager->findUserByEmail(urldecode($email)))
        {
            throw $this->createNotFoundException(sprintf('Użytkownik o podanym adresie e-mail: "%s", nie istnieje.', urldecode($email)));
        }

        if($code !== hash('sha256', $user->getEmail().$user->getPassword()))
        {
            throw $this->createNotFoundException(sprintf('Podany kod: "%s", jest niepoprawny.', $code));
        }

        if($user->getEnabled() !== 0)
        {
            throw $this->createNotFoundException(sprintf('Użytkownik jest już aktywowany lub zablokowany. Status: "%s".', $user->getEnabled()));
        }

        $user->setEnabled(1);
        $userManager->updateUser($user);

        $this->getSession()->getFlashBag()->set('msg', 'Konto zostało aktywowane. Możesz się już zalogować.');

        return $this->redirect($this->generateUrl('homepage'));
    }

    public function registerResendAction(Request $request)
    {
        /** @var \Sequence\User\User $user */
        $user = false;
        $error = array();
        $email = $request->request->get('email');

        if($this->getUser()->isUser())
        {
            return $this->redirect($this->generateUrl('homepage'));
        }

        if($request->isMethod('post'))
        {
            /** @var \Sequence\User\UserManager $userManager */
            $userManager = $this->get('user_manager');

            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $error = 'Wpisany adres e-mail jest niepoprawny';
            }

            if(empty($error) && false == $user = $userManager->findUserByEmail($email))
            {
                $error = 'Wpisany adres e-mail nie został odnaleziony.';
            }

            if(false !== $user && $user->getEnabled() !== 0)
            {
                if($user->getEnabled() == 1)
                {
                    $error = 'Konto do którego próbujesz wysłać list aktywacyjny jest aktywne.';
                } elseif($user->getEnabled() == 2)
                {
                    $error = 'Konto do któego próbujesz wysłać list aktywacyjny jest zablokowane. Użyj formularza kontaktowego w celu wyjaśnienia sytuacji.';
                }
            }

            //check code
            if(strcmp($request->request->get('code'), $this->getSession()->get('captcha')))
            {
                $error = 'Przepisany kod jest niepoprawny.';
            } else
            {
                $this->getSession()->remove('captcha');
            }

            if(empty($error))
            {
                $id = hash('sha256', $user->getEmail().$user->getPassword());

                //send email with activation code
                /** @var \Sequence\Mail\Mailer $mail */
                $mail = $this->get('mailer');
                $mail->addAddress($user->getEmail());
                $mail->Subject = $title ='Potwierdzenie rejstracji na AnimeZone.pl';
                $mail->Body = $this->renderView('Mail/User/register', array(
                    'title' => $title,
                    'login' => $user->getUsername(),
                    'code' => $id,
                    'email' => urlencode($user->getEmail()),
                ));
                $mail->send();

                $this->getSession()->getFlashBag()->set('msg', 'Na podany przez ciebie adres e-mail został przesłany list aktywacyjny.');

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render('User/resend', array(
            'email' => $email,
            'error' => $error,
            'sidebar' => $this->faqManager->findAll(),
        ));
    }

    public function restoreAction(Request $request)
    {
        /** @var \Sequence\User\User $user */
        $user = false;
        $error = null;
        $email = $request->request->get('email');

        if($this->getUser()->isUser())
        {
            return $this->redirect($this->generateUrl('homepage'));
        }

        if($request->isMethod('post'))
        {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $error = 'Wpisany adres e-mail jest niepoprawny';
            }

            if(false == $user = $this->get('user_manager')->findUserByEmail($email))
            {
                $error = 'Wpisany adres e-mail nie został odnaleziony.';
            }

            if(false !== $user && !$user->isEnabled())
            {
                if($user->getEnabled() == 0)
                {
                    $error = 'Konto do którego próbujesz zmienić hasło nie zostało aktywowane.
                    Sprawdź swoją skrzynkę e-mail w celu aktywacji konta, lub ponownie wyślij list aktywacyjny pod adresem:
                    <a href="'.$this->generateUrl('user_register_resend').'">wyślij ponownie</a>.';
                } elseif($user->getEnabled() == 2)
                {
                    $error = 'Konto do któego próbujesz zmienić hasło jest zablokowane. Użyj formularza kontaktowego w celu wyjaśnienia sytuacji.';
                }
            }

            if(false !== $user && $user->getCustomField('facebook_id') > 0)
            {
                $error = 'Konto do którego próbujesz zmienić hasło zostało zintegrowane z facebook.com,
                aby móc się zalogować wykorzystaj do tego celu <a href="'.$this->generateUrl('login_facebook').'">logowanie z facebookiem</a>.';
            }

            //check code
            if(strcmp($request->request->get('code'), $this->getSession()->get('captcha')))
            {
                $error = 'Przepisany kod jest niepoprawny.';
            } else
            {
                $this->getSession()->remove('captcha');
            }

            if(null == $error)
            {
                $id = hash('sha256', uniqid().$user->getPassword());

                $restoreManager = new RestoreManager($this->getDatabase());
                $restoreManager->update($restoreManager->create(array(
                    'code' => $id,
                    'user_id' => $user->getId(),
                )));

                //send email with restore code
                /** @var \Sequence\Mail\Mailer $mail */
                $mail = $this->get('mailer');
                $mail->addAddress($user->getEmail());
                $mail->Subject = $title ='Zmiana hasła na AnimeZone.pl';
                $mail->Body = $this->renderView('Mail/User/restore_passowrd', array(
                    'title' => $title,
                    'login' => $user->getUsername(),
                    'code' => $id,
                ));
                $mail->send();

                $this->getSession()->getFlashBag()->set('msg', 'Wiadomość e-mail z kodem potiwerdzającym została przesłana na podany adres e-mail: '.$email);

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render('User/restore', array(
            'email' => $email,
            'error' => $error,
            'sidebar' => $this->faqManager->findAll(),
        ));
    }

    public function restoreConfirmAction($code, Request $request)
    {
        $error = null;
        $password = $request->request->get('password');
        $password2 = $request->request->get('password2');

        if($this->getUser()->isUser())
        {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $restoreManager = new RestoreManager($this->getDatabase());

        //delete too old codes
        $restoreManager->deleteOldCodes();

        if(false == $data = $restoreManager->findOneBy(array('code' => $code)))
        {
            throw $this->createNotFoundException(sprintf('Podany kod "%s" nie istnieje w bazie danych.', $code));
        }

        if($request->isMethod('post'))
        {
            if(!in_array(strlen($password), range(5, 32)))
            {
                $error = 'Poadane hasło jest nieprawidłowe. Minimum 5 znaków, maksimum 32.';
            }

            if($password !== $password2)
            {
                $error = 'Podane hasło jest różne niż hasło potwierdzające.';
            }

            if(null == $error)
            {
                $restoreManager->delete($restoreManager->create($data));

                /** @var \Sequence\User\User $user */
                $user = $this->get('user_manager')->findUserBy(array('id' => $data['user_id']));
                $user->setPassword($password);

                $this->get('user_manager')->updateUser($user);

                $this->get('dispatcher')->addSubscriber(new UserListener(new WatchManager($this->getDatabase())));

                $this->get('user.login_manager')->login($user->getEmail(), $password);

                $this->getSession()->getFlashBag()->set('msg', 'Twoje hasło zostało zmienione. Gdybyś chciał je zmienić na inne,
                    skorzystaj w tym celu z <a href="'.$this->generateUrl('user_edit_profile').'">edycji profilu</a>.');

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render('User/restore_confirm', array(
            'code' => $code,
            'error' => $error,
            'sidebar' => $this->faqManager->findAll(),
        ));
    }
} 