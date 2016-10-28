<?php


namespace Admin\Controller;


use Anime\Model\Category\Category;
use Anime\Model\Type\TypeManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class TypesController extends Controller
{
    /** @var  \Anime\Model\Type\TypeManager */
    protected $types;

    public function init()
    {
        $this->types = new TypeManager($this->getDatabase());

        $this->get('templating')->addGlobal('_status', (new Category())->getStatusType());
    }

    public function indexAction(Request $request)
    {
        $list = $this->types->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('types_index'))->
            setUrl($this->generateUrl('types_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Types/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $type = $this->types->create($request->request->get('type'));

            $this->types->update($type);
            $this->getSession()->getFlashBag()->add('msg', sprintf('Typ <strong>%s</strong> został utworzony.', $type->getName()));

            return $this->redirect($this->generateUrl('types_index'));
        }

        return $this->render('Types/create');
    }

    public function editAction($typeID, Request $request)
    {
        if(!$type = $this->types->find($typeID))
        {
            throw $this->createNotFoundException();
        }

        $type = $this->types->create($type);

        if($request->isMethod('post'))
        {
            $new = $this->types->create($request->request->get('type'));

            $this->types->update($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Typ <strong>%s</strong> został zaktualizowany na <strong>%s</strong>.', $type->getName(), $new->getName()));

            return $this->redirect($this->generateUrl('types_index'));
        }

        return $this->render('Types/edit', array(
            'type' => $type,
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
                $this->types->deleteWhere(array('id' => $id));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Typy zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone typy (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('types_update'),
            ));
        }

        return $this->redirect($this->generateUrl('types_index'));
    }

    public function deleteAction($typeID, Request $request)
    {
        if(!$type = $this->types->find($typeID))
        {
            throw $this->createNotFoundException();
        }

        $type = $this->types->create($type);

        if($request->isMethod('post'))
        {
            if($typeID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->types->delete($type);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Typ <strong>%s</strong> i powiązania został usunięty.', $type->getName()));

            return $this->redirect($this->generateUrl('types_index'));
        }

        return $this->render('Types/delete', array(
            'type' => $type,
        ));
    }
} 