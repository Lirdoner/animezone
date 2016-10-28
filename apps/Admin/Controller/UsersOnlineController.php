<?php


namespace Admin\Controller;


use Anime\Model\User\UsersOnlineManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class UsersOnlineController extends Controller
{
    /** @var  \Anime\Model\User\UsersOnlineManager */
    protected $sessions;

    public function init()
    {
        $this->sessions = new UsersOnlineManager($this->getDatabase());
    }

    public function indexAction(Request $request)
    {
        $list = $this->sessions->findList();

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('sessions_index'))->
            setUrl($this->generateUrl('sessions_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('last_active DESC')->get();

        return $this->render('Sessions/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function viewAction($sessID)
    {
        if(!$session = $this->sessions->find($sessID))
        {
            throw $this->createNotFoundException();
        }

        return $this->render('Sessions/view', array(
            'session' => $session,
        ));
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
                $this->sessions->deleteWhere(array('sess_id' => $id));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Sesje (<strong>%s</strong>) zostały usunięte.', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone sesje (<strong>%s</strong>)?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('sessions_update'),
            ));
        }

        return $this->redirect($this->generateUrl('sessions_index'));
    }

    public function deleteAction($sessID, Request $request)
    {
        if('all' == $sessID)
        {
            $session['sess_id'] = 'all';
        } else
        {
            if(!$session = $this->sessions->find($sessID))
            {
                throw $this->createNotFoundException();
            }
        }

        if($request->isMethod('post'))
        {
            if('all' == $sessID)
            {
                $mySessionId = $this->getSession()->getId();

                $this->sessions->deleteWhere(array('sess_id !=' => $mySessionId));
                $msg = sprintf('Wszystkie sesje z wyjątkiem twojej (<strong>%s</strong>) zostały usunięte.', $mySessionId);
            } else
            {
                $this->sessions->deleteWhere(array('sess_id' => $sessID));
                $msg = sprintf('Sessja <strong>%s</strong> została usunięta.', $sessID);
            }

            $this->getSession()->getFlashBag()->add('msg', $msg);

            return $this->redirect($this->generateUrl('sessions_index'));
        }

        return $this->render('Sessions/delete', array(
            'session' => $session,
        ));
    }

    public function searchAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            return $this->redirect($this->generateUrl('sessions_search', array(
                'name' => ($request->request->get('name') === '' ? null : $request->request->get('name')),
                'user_ip' => ($request->request->get('user_ip') === '' ? null : $request->request->get('user_ip')),
                'user_agent' => ($request->request->get('user_agent') === '' ?  null : $request->request->get('user_agent')),
                'user_role' => ($request->request->get('user_role') === '' ?  null : $request->request->get('user_role')),
            )));
        }

        $query = array();

        if($request->query->has('name'))
        {
            $query['name LIKE'] = '%'.$request->query->get('name').'%';
        }

        if($request->query->has('user_ip'))
        {
            $query['user_ip LIKE'] = '%'.$request->query->get('user_ip').'%';
        }

        if($request->query->has('user_agent'))
        {
            $query['user_agent LIKE'] = '%'.$request->query->get('user_agent').'%';
        }

        if($request->query->has('user_role'))
        {
            $query['user_role'] = $request->query->get('user_role');
        }

        if(empty($query))
        {
            return $this->redirect($this->generateUrl('sessions_index'));
        }

        $list = $this->sessions->findList()->where($query);

        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('sessions_search', array(
                'name' => $request->query->get('name'),
                'user_ip' => $request->query->get('user_ip'),
                'user_agent' => $request->query->get('user_agent'),
                'user_role' => $request->query->get('user_role'),
            )))->
            setUrl($this->generateUrl('sessions_search', array(
                'page' => '_PAGE_',
                'name' => $request->query->get('name'),
                'user_ip' => $request->query->get('user_ip'),
                'user_agent' => $request->query->get('user_agent'),
                'user_role' => $request->query->get('user_role'),
            )))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('last_active DESC')->get();

        return $this->render('Sessions/search', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
            'name' => $request->query->get('name'),
            'user_ip' => $request->query->get('user_ip'),
            'user_agent' => $request->query->get('user_agent'),
            'user_role' => $request->query->get('user_role'),
        ));
    }
} 