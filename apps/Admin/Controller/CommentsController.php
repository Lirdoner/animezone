<?php


namespace Admin\Controller;


use Anime\Model\Comment\CommentManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class CommentsController extends Controller
{
    /** @var  \Anime\Model\Comment\CommentManager */
    protected $comments;

    public function init()
    {
        $this->comments = new CommentManager($this->getDatabase());
    }

    public function indexAction(Request $request)
    {
        $list = $this->comments->findList();

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('comments_index'))->
            setUrl($this->generateUrl('comments_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('date DESC')->get();

        return $this->render('Comments/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function editAction($commentID, Request $request)
    {
        if(!$comment = $this->comments->find($commentID))
        {
            throw $this->createNotFoundException();
        }

        $comment = $this->comments->create($comment);

        if($request->isMethod('post'))
        {
            $new = $this->comments->create($request->request->get('comment'));
            $new->setDate(new \DateTime($comment->getDate()));

            $this->comments->update($new);

            $this->getSession()->getFlashBag()->add('msg', 'Komentarz został zaktualizowany');

            return $this->redirect($this->generateUrl('comments_index'));
        }

        return $this->render('Comments/edit', array(
            'comment' => $comment,
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
                $this->comments->deleteWhere(array('id' => $id));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Komentarze zostały usuniete (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone komentarze (<strong>%s</strong>)?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('comments_update'),
            ));
        }

        return $this->redirect($this->generateUrl('comments_index'));
    }

    public function deleteAction($commentID, Request $request)
    {
        if(!$comment = $this->comments->find($commentID))
        {
            throw $this->createNotFoundException();
        }

        $comment = $this->comments->create($comment);

        if($request->isMethod('post'))
        {
            if($commentID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->getDatabase()->beginTransaction();

            $this->comments->delete($comment);

            //update user profile
            /** @var \Sequence\User\UserManager $manager */
            $manager = $this->get('user_manager');

            $user = $manager->findUserBy(array('id' => $comment->getUserId()));
            $user->setCustomField('commented', $this->comments->count(array('user_id' => $user->getId())));

            $manager->updateUser($user);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Komentarz <strong>%s</strong> został usunięty.', $comment->getId()));

            $this->getDatabase()->commit();

            return $this->redirect($this->generateUrl('comments_index'));
        }

        return $this->render('Comments/delete', array(
            'comment' => $comment,
        ));
    }

    public function searchAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            return $this->redirect($this->generateUrl('comments_search', array(
                'type' => ($request->request->get('type') === '' ? null : $request->request->get('type')),
                'message' => ($request->request->get('message') === '' ? null : $request->request->get('message'))
            )));
        }

        $query = array();

        if($request->query->has('message'))
        {
            $query['message LIKE'] = '%'.$request->query->get('message').'%';
        }

        if($request->query->has('to'))
        {
            $query['to'] = $request->query->get('to');
        }

        if($request->query->has('type'))
        {
            $query['type'] = $request->query->get('type');
        }

        if($request->query->has('user_id'))
        {
            $query['user_id'] = $request->query->get('user_id');
        }

        if(empty($query))
        {
            return $this->redirect($this->generateUrl('comments_index'));
        }

        $list = $this->comments->findList()->where($query);

        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('comments_search', array(
                'message' => $request->query->get('message'),
                'to' => $request->query->get('to'),
                'type' => $request->query->get('type'),
                'user_id' => $request->query->get('user_id'),
            )))->
            setUrl($this->generateUrl('comments_search', array(
                'page' => '_PAGE_',
                'message' => $request->query->get('message'),
                'to' => $request->query->get('to'),
                'type' => $request->query->get('type'),
                'user_id' => $request->query->get('user_id'),
            )))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('date DESC')->get();

        return $this->render('Comments/search', array(
            'total' => $total,
            'list' => $list,
            'message' => $request->query->get('message'),
            'type' => $request->query->get('type', false),
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }
} 