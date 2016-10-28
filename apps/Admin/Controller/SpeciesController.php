<?php


namespace Admin\Controller;


use Anime\Model\Category\Category;
use Anime\Model\Species\Species;
use Anime\Model\Species\SpeciesManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class SpeciesController extends Controller
{
    /** @var  \Anime\Model\Species\SpeciesManager */
    protected $species;

    public function init()
    {
        $this->species = new SpeciesManager($this->getDatabase());

        $this->get('templating')->addGlobal('_status', (new Category())->getStatusType());
    }

    public function indexAction(Request $request)
    {
        $list = $this->species->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('species_index'))->
            setUrl($this->generateUrl('species_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Species/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $species = new Species($request->request->get('species'));

            $this->species->update($species);
            $this->getSession()->getFlashBag()->add('msg', sprintf('Gatunek <strong>%s</strong> został utworzony.', $species->getName()));

            return $this->redirect($this->generateUrl('species_index'));
        }

        return $this->render('Species/create');
    }

    public function editAction($speciesID, Request $request)
    {
        if(!$species = $this->species->findOneBy(array('id' => $speciesID)))
        {
            throw $this->createNotFoundException();
        }

        $species = new Species($species);

        if($request->isMethod('post'))
        {
            $new = new Species($request->request->get('species'));

            $this->species->update($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Gatunek <strong>%s</strong> został zaktualizowany na <strong>%s</strong>.', $species->getName(), $new->getName()));

            return $this->redirect($this->generateUrl('species_index'));
        }

        return $this->render('Species/edit', array(
            'species' => $species,
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
                $this->species->delete(new Species(array('id' => $id)));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Gatunki zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone gatunki (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('species_update'),
            ));
        }

        return $this->redirect($this->generateUrl('species_index'));
    }

    public function deleteAction($speciesID, Request $request)
    {
        if(!$species = $this->species->findOneBy(array('id' => $speciesID)))
        {
            throw $this->createNotFoundException();
        }

        $species = new Species($species);

        if($request->isMethod('post'))
        {
            if($speciesID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->species->delete($species);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Gatunek <strong>%s</strong> i powiązania został usunięty.', $species->getName()));

            return $this->redirect($this->generateUrl('species_index'));
        }

        return $this->render('Species/delete', array(
            'species' => $species,
        ));
    }
} 