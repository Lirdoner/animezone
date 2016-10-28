<?php


namespace Admin\Controller;


use Anime\Model\Pages\Pages;
use Anime\Model\Pages\PagesManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PagesController extends Controller
{
    /** @var  \Anime\Model\Pages\PagesManager */
    protected $pages;

    public function init()
    {
        $this->pages = new PagesManager($this->getDatabase());
    }

    public function indexAction(Request $request)
    {
        $list = $this->pages->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('pages_index'))->
            setUrl($this->generateUrl('pages_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Pages/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $page = new Pages($request->request->get('page'));

            $this->pages->update($page);
            $this->getSession()->getFlashBag()->add('msg', sprintf('Podstrona <strong>%s</strong> została utworzona.', $page->getName()));

            return $this->redirect($this->generateUrl('pages_index'));
        }

        return $this->render('Pages/create');
    }

    public function editAction($pageID, Request $request)
    {
        if(!$page = $this->pages->findOneBy(array('id' => $pageID)))
        {
            throw $this->createNotFoundException();
        }

        $page = new Pages($page);

        if($request->isMethod('post'))
        {
            $new = new Pages($request->request->get('page'));

            $this->pages->update($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Podstrona <strong>%s</strong> została zaktualizowana.', $new->getName()));

            return $this->redirect($this->generateUrl('pages_index'));
        }

        return $this->render('Pages/edit', array(
            'page' => $page,
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
                $this->pages->delete(new Pages(array('id' => $id)));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Podstrony zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone podstrony (<strong>%s</strong>) wraz z treścią?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('pages_update'),
            ));
        }

        return $this->redirect($this->generateUrl('pages_index'));
    }

    public function deleteAction($pageID, Request $request)
    {
        if(!$page = $this->pages->findOneBy(array('id' => $pageID)))
        {
            throw $this->createNotFoundException();
        }

        $page = new Pages($page);

        if($request->isMethod('post'))
        {
            if($pageID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->pages->delete($page);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Podstrona <strong>%s</strong> wraz z treścią, została usunięta.', $page->getName()));

            return $this->redirect($this->generateUrl('pages_index'));
        }

        return $this->render('Pages/delete', array(
            'page' => $page,
        ));
    }

    public function aliasAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->request->has('query'))
        {
            $category = $this->pages->findOneBy(array('alias' => $request->request->get('query')));

            $response = array();

            if(false !== $category)
            {
                $response['id'] = $category['id'];
                $response['name'] = $category['name'];
            }

            return new JsonResponse($response);
        }

        throw $this->createNotFoundException();
    }
} 