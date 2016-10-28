<?php


namespace Admin\Controller;


use Anime\Model\Category\Category;
use Anime\Model\Category\CategoryManager;
use Anime\Model\Series\Series;
use Anime\Model\Series\SeriesManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class SeriesController extends Controller
{
    /** @var  \Anime\Model\Series\SeriesManager */
    protected $series;

    public function init()
    {
        $this->series = new SeriesManager($this->getDatabase());

        $this->get('templating')->addGlobal('_status', (new Category())->getStatusType());
    }

    public function indexAction(Request $request)
    {
        $list = $this->series->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('series_index'))->
            setUrl($this->generateUrl('series_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('name ASC')->get();

        return $this->render('Series/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $cycle = new Series($request->request->get('cycle'));

            $this->series->update($cycle);
            $this->getSession()->getFlashBag()->add('msg', sprintf('Seria <strong>%s</strong> została utworzona.', $cycle->getName()));

            return $this->redirect($this->generateUrl('series_index'));
        }

        return $this->render('Series/create');
    }

    public function editAction($rowID, Request $request)
    {
        if(!$cycle = $this->series->findOneBy(array('id' => $rowID)))
        {
            throw $this->createNotFoundException();
        }

        $cycle = new Series($cycle);

        if($request->isMethod('post'))
        {
            $new = new Series($request->request->get('cycle'));

            $this->series->update($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Seria <strong>%s</strong> został zaktualizowana na <strong>%s</strong>.', $cycle->getName(), $new->getName()));

            return $this->redirect($this->generateUrl('series_index'));
        }

        return $this->render('Series/edit', array(
            'cycle' => $cycle,
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

            $categoryManager = new CategoryManager($this->getDatabase(), $this->getCache());

            foreach($toDelete as $id)
            {
                $this->series->delete(new Series(array('id' => $id)));
                $categoryManager->clearSeries($id);
            }

            $categoryManager->clearCache();

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Serie zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone serie (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('series_update'),
            ));
        }

        return $this->redirect($this->generateUrl('series_index'));
    }

    public function deleteAction($rowID, Request $request)
    {
        if(!$cycle = $this->series->findOneBy(array('id' => $rowID)))
        {
            throw $this->createNotFoundException();
        }

        $cycle = new Series($cycle);

        if($request->isMethod('post'))
        {
            if($rowID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->series->delete($cycle);

            $categoryManager = new CategoryManager($this->getDatabase(), $this->getCache());
            $categoryManager->clearSeries($cycle->getId());
            $categoryManager->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('Seria <strong>%s</strong> i powiązania zostały usunięte.', $cycle->getName()));

            return $this->redirect($this->generateUrl('series_index'));
        }

        return $this->render('Series/delete', array(
            'cycle' => $cycle,
        ));
    }
} 