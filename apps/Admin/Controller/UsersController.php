<?php


namespace Admin\Controller;


use Anime\Model\User\UsersOnlineManager;
use Sequence\Controller;
use Sequence\User\User;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller
{
    /** @var  \Sequence\User\UserManager */
    protected $users;

    public function init()
    {
        $this->users = $this->get('user_manager');
    }

    public function indexAction(Request $request)
    {
        $list = $this->users->findUsers();

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('users_index'))->
            setUrl($this->generateUrl('users_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('date_created DESC')->get();

        return $this->render('Users/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $user = $this->users->createUser($request->request->get('user'));
            $user->setPassword(uniqid());

            $this->users->updateUser($user);
            $this->getSession()->getFlashBag()->add('msg', sprintf('Użytkownik <strong>%s</strong> został utworzony.', $user->getUsername()));

            return $this->redirect($this->generateUrl('users_index'));
        }

        return $this->render('Users/create');
    }

    public function editAction($userID, Request $request)
    {
        if(!$user = $this->users->findUserBy(array('id' => $userID)))
        {
            throw $this->createNotFoundException();
        }

        if($request->isMethod('post'))
        {
            $data = $request->request->get('user');

            if(empty($data['password']))
            {
                unset($data['password']);
                $password = false;
            } else
            {
                $password = $data['password'];
            }

            $new = $this->users->createUser($data);

            if($password && $new->getId())
            {
                $new->setPassword($password);

                $sessManager = new UsersOnlineManager($this->getDatabase());
                $sessManager->deleteWhere(array('user_id' => $new->getId()));
            }

            $this->users->updateUser($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Użytkownik <strong>%s</strong> został zaktualizowany.', $new->getUsername()));

            return $this->redirect($this->generateUrl('users_index'));
        }

        return $this->render('Users/edit', array(
            'user' => $user,
        ));
    }

    public function changeAction($userID, $action, $value, Request $request)
    {
        if(!$user = $this->users->findUserBy(array('id' => $userID)))
        {
            throw $this->createNotFoundException();
        }

        $session = $this->getSession();

        if('enabled' == $action)
        {
            $user->setEnabled($value);
            $session->getFlashBag()->add('msg', sprintf('Użytkownik <strong>%s</strong> został <ins>%s</ins>.', $user->getUsername(), $value == 1 ? 'odblokowany' : 'zablokowany'));
        } elseif('role' == $action)
        {
            $user->setAdmin(User::ROLE_ADMIN == $value ? true : false);
            $session->getFlashBag()->add('msg', sprintf('Użytkownikowi <strong>%s</strong> zostały <ins>%s</ins> uprawnienia administratora.', $user->getUsername(), User::ROLE_ADMIN == $value ? 'nadane' : 'odebrane'));
        }

        $this->users->updateUser($user);

        $usersOnline = new UsersOnlineManager($this->getDatabase());
        $usersOnline->deleteWhere(array('user_id' => $user->getId()));

        return $this->redirect($request->headers->get('referer', $this->generateUrl('users_index')));
    }

    public function updateAction(Request $request)
    {
        $session = $this->getSession();

        if($request->request->has('delete'))
        {
            $session->set('to_delete', $request->request->get('delete'));
        }

        if($request->request->has('confirm') && $session->has('to_delete'))
        {
            $toDelete = $session->get('to_delete');

            if(!is_array($toDelete))
            {
                $toDelete = array(0 => $toDelete);
            }

            foreach($toDelete as $id)
            {
                $this->users->deleteUser(new User(array('id' => $id)));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Użytkownicy (<strong>%s</strong>) zostali usunieci wraz z powiązaniami.', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkich zaznaczonych użytkowników (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('users_update'),
            ));
        }

        return $this->redirect($this->generateUrl('users_index'));
    }

    public function deleteAction($userID, Request $request)
    {
        if(!$user = $this->users->findUserBy(array('id' => $userID)))
        {
            throw $this->createNotFoundException();
        }

        if($request->isMethod('post'))
        {
            if($userID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->users->deleteUser($user);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Użytkownik <strong>%s</strong> i powiązania zostały usunięte.', $user->getUsername()));

            return $this->redirect($this->generateUrl('users_index'));
        }

        return $this->render('Users/delete', array(
            'user' => $user,
        ));
    }

    public function searchAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            return $this->redirect($this->generateUrl('users_search', array(
                'name' => ($request->request->get('name') === '' ? null : $request->request->get('name')),
                'email' => ($request->request->get('email') === '' ? null : $request->request->get('email')),
                'ip' => ($request->request->get('ip') === '' ? null : $request->request->get('ip')),
                'location' => ($request->request->get('location') === '' ?  null : $request->request->get('location')),
                'gender' => ($request->request->get('gender') === '' ?  null : $request->request->get('gender')),
                'enabled' => ($request->request->get('enabled') === '' ?  null : $request->request->get('enabled')),
                'role' => ($request->request->get('role') === '' ?  null : $request->request->get('role')),
            )));
        }

        $query = array();

        if($request->query->has('name'))
        {
            $query['name LIKE'] = '%'.$request->query->get('name').'%';
        }

        if($request->query->has('email'))
        {
            $query['email LIKE'] = '%'.$request->query->get('email').'%';
        }

        if($request->query->has('ip'))
        {
            $query['ip LIKE'] = '%'.$request->query->get('ip').'%';
        }

        if($request->query->has('location'))
        {
            $query['location LIKE'] = '%'.$request->query->get('location').'%';
        }

        if($request->query->has('gender'))
        {
            $query['gender'] = $request->query->get('gender');
        }

        if($request->query->has('enabled'))
        {
            $query['enabled'] = $request->query->get('enabled');
        }

        if($request->query->has('role'))
        {
            $query['role'] = $request->query->get('role');
        }

        if(empty($query))
        {
            return $this->redirect($this->generateUrl('users_index'));
        }

        $list = $this->users->findUsers($query);

        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('users_search', array(
                'name' => $request->query->get('name'),
                'email' => $request->query->get('email'),
                'ip' => $request->query->get('ip'),
                'location' => $request->query->get('location'),
                'gender' => $request->query->get('gender'),
                'enabled' => $request->query->get('enabled'),
                'role' => $request->query->get('role'),
            )))->
            setUrl($this->generateUrl('users_search', array(
                'page' => '_PAGE_',
                'name' => $request->query->get('name'),
                'email' => $request->query->get('email'),
                'ip' => $request->query->get('ip'),
                'location' => $request->query->get('location'),
                'gender' => $request->query->get('gender'),
                'enabled' => $request->query->get('enabled'),
                'role' => $request->query->get('role'),
            )))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Users/search', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
            'name' => $request->query->get('name'),
            'email' => $request->query->get('email'),
            'ip' => $request->query->get('ip'),
            'location' => $request->query->get('location'),
            'gender' => $request->query->get('gender'),
            'enabled' => $request->query->get('enabled'),
            'role' => $request->query->get('role'),
        ));
    }

    public function checkAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->request->has('query'))
        {
            $user = $this->users->findUserByUsernameOrEmail($request->request->get('query'));

            $response = array();

            if($user)
            {
                $response['id'] = $user->getId();
                $response['name'] = $user->getUsername();
                $response['email'] = $user->getEmail();
            }

            return new JsonResponse($response);
        }

        throw $this->createNotFoundException();
    }

    public function statsAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            if(null === $data = $this->getCache()->get('users/stats'))
            {
                $data = $this->getDatabase()->query('
                    SELECT
                    COUNT(`id`) AS `all`,
                    SUM(if(`enabled`=0, 1, 0)) AS `inactive`,
                    SUM(if(`enabled`=2, 1, 0)) AS `blocked`,
                    SUM(if(`gender`=1, 1, 0)) AS `man`,
                    SUM(if(`gender`=2, 1, 0)) AS `woman`
                    FROM `users`
                    JOIN `users_custom_field` ON `id`=`user_id`
                ');

                $data = $data->fetch();

                $this->getCache()->set('users/stats', $data, 86400);
            }

            return new JsonResponse($data);
        }

        throw $this->createNotFoundException();
    }
} 