<?php


namespace Admin\Controller;


use Anime\Model\Category\Category;
use Anime\Model\Topics\TopicsManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class TopicsController extends Controller
{
    /** @var  \Anime\Model\Topics\TopicsManager */
    protected $topics;

    public function init()
    {
        $this->topics = new TopicsManager($this->getDatabase());

        $this->get('templating')->addGlobal('_status', (new Category())->getStatusType());
    }

    public function indexAction(Request $request)
    {
        $list = $this->topics->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('topics_index'))->
            setUrl($this->generateUrl('topics_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Topics/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $topic = $this->topics->create($request->request->get('topic'));

            $this->topics->update($topic);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Tematyka <strong>%s</strong> została utworzona.', $topic->getName()));

            return $this->redirect($this->generateUrl('topics_index'));
        }

        return $this->render('Topics/create');
    }

    public function editAction($topicID, Request $request)
    {
        if(!$topic = $this->topics->find($topicID))
        {
            throw $this->createNotFoundException();
        }

        $topic = $this->topics->create($topic);

        if($request->isMethod('post'))
        {
            $new = $this->topics->create($request->request->get('topic'));

            $this->topics->update($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Tematyka <strong>%s</strong> została zaktualizowana na <strong>%s</strong>.', $topic->getName(), $new->getName()));

            return $this->redirect($this->generateUrl('topics_index'));
        }

        return $this->render('Topics/edit', array(
            'topic' => $topic,
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
                $this->topics->deleteWhere(array('id' => $id));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Tematyki zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone tematyki (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('topics_update'),
            ));
        }

        return $this->redirect($this->generateUrl('topics_index'));
    }

    public function deleteAction($topicID, Request $request)
    {
        if(!$topic = $this->topics->find($topicID))
        {
            throw $this->createNotFoundException();
        }

        $topic = $this->topics->create($topic);

        if($request->isMethod('post'))
        {
            if($topicID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->topics->delete($topic);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Tematyka <strong>%s</strong> i powiązania został usunięty.', $topic->getName()));

            return $this->redirect($this->generateUrl('topics_index'));
        }

        return $this->render('Topics/delete', array(
            'topic' => $topic,
        ));
    }
} 