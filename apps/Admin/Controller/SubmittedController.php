<?php


namespace Admin\Controller;


use Anime\Model\SubmittedEpisode\SubmittedEpisodeManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class SubmittedController extends Controller
{
    /** @var  \Anime\Model\SubmittedEpisode\SubmittedEpisodeManager */
    protected $submitted;

    public function init()
    {
        $this->submitted = new SubmittedEpisodeManager($this->getDatabase());
    }

    public function indexAction(Request $request)
    {
        $list = $this->submitted->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('submitted_index'))->
            setUrl($this->generateUrl('submitted_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Submitted/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function viewAction($episodeID)
    {
        if(!$submitted = $this->submitted->find($episodeID))
        {
            throw $this->createNotFoundException();
        }

        return $this->render('Submitted/view', array(
            'submitted' => $this->submitted->create($submitted),
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
                $this->submitted->deleteWhere(array('id' => $id));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Linki zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone linki (<strong>%s</strong>)?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('submitted_update'),
            ));
        }

        return $this->redirect($this->generateUrl('submitted_index'));
    }

    public function deleteAction($episodeID, Request $request)
    {
        if(!$submitted = $this->submitted->find($episodeID))
        {
            throw $this->createNotFoundException();
        }

        $submitted = $this->submitted->create($submitted);

        if($request->isMethod('post'))
        {
            if($episodeID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->submitted->delete($submitted);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Link <strong>%s</strong> został usunięty.', $submitted->getTitle()));

            return $this->redirect($this->generateUrl('submitted_index'));
        }

        return $this->render('Submitted/delete', array(
            'submitted' => $submitted,
        ));
    }
} 