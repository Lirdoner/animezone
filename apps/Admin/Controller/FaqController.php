<?php


namespace Admin\Controller;


use Anime\Model\Faq\Faq;
use Anime\Model\Faq\FaqManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class FaqController extends Controller
{
    /** @var  \Anime\Model\Faq\FaqManager */
    protected $faq;

    public function init()
    {
        $this->faq = new FaqManager($this->getDatabase());
    }

    public function indexAction(Request $request)
    {
        $list = $this->faq->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('faq_index'))->
            setUrl($this->generateUrl('faq_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Faq/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $faq = new Faq($request->request->get('faq'));

            $this->faq->update($faq);
            $this->getSession()->getFlashBag()->add('msg', sprintf('Pytanie <strong>%s</strong> zostało utworzone.', $faq->getQuestion()));

            return $this->redirect($this->generateUrl('faq_index'));
        }

        return $this->render('Faq/create');
    }

    public function editAction($faqID, Request $request)
    {
        if(!$faq = $this->faq->findOneBy(array('id' => $faqID)))
        {
            throw $this->createNotFoundException();
        }

        $faq = new Faq($faq);

        if($request->isMethod('post'))
        {
            $new = new Faq($request->request->get('faq'));

            $this->faq->update($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Pytanie <strong>%s</strong> zostało zaktualizowane.', $new->getQuestion()));

            return $this->redirect($this->generateUrl('faq_index'));
        }

        return $this->render('Faq/edit', array(
            'faq' => $faq,
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
                $this->faq->delete(new Faq(array('id' => $id)));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Pytania zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone pytania (<strong>%s</strong>) wraz z odpowiedziami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('faq_update'),
            ));
        }

        return $this->redirect($this->generateUrl('faq_index'));
    }

    public function deleteAction($faqID, Request $request)
    {
        if(!$faq = $this->faq->findOneBy(array('id' => $faqID)))
        {
            throw $this->createNotFoundException();
        }

        $faq = new Faq($faq);

        if($request->isMethod('post'))
        {
            if($faqID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->faq->delete($faq);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Pytanie <strong>%s</strong> i odpowiedź zostało usunięte.', $faq->getQuestion()));

            return $this->redirect($this->generateUrl('faq_index'));
        }

        return $this->render('Faq/delete', array(
            'faq' => $faq,
        ));
    }
} 