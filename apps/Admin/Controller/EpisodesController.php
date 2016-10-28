<?php





namespace Admin\Controller;





use Anime\Model\Category\CategoryManager;

use Anime\Model\Episode\EpisodeManager;

use Anime\Model\Link\LinkManager;

use Sequence\Controller;

use Sequence\Util\Pagination;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;



class EpisodesController extends Controller

{

    /** @var  \Anime\Model\Episode\EpisodeManager */

    protected $episodes;



    public function init()

    {

        /** @var \Sequence\Cache\Cache $cache */

        $cache = $this->get('front_cache');



        $this->episodes = new EpisodeManager($this->getDatabase(), $cache);

    }



    public function indexAction(Request $request)

    {

        $list = $this->episodes->findList();



        $pagination =  new Pagination();

        $pagination->

        setBaseUrl($this->generateUrl('episodes_index'))->

        setUrl($this->generateUrl('episodes_index', array('page' => '_PAGE_')))->

        setPerPage(20)->

        setRange(2)->

        setUrlNeedle('_PAGE_')->

        setTotalCount($total = $list->get()->rowCount())->

        setCurrentPage($request->query->get('page', 1));



        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();



        return $this->render('Episodes/index', array(

            'total' => $total,

            'list' => $list,

            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),

        ));

    }



    public function createAction(Request $request)

    {

        if($request->isMethod('post'))

        {

            $episode = $this->episodes->create($request->request->get('episode'));



            $this->episodes->update($episode);

            $this->episodes->clearCache();



            $this->getSession()->getFlashBag()->add('msg', sprintf('Odcinek <strong>%s</strong> został utworzony.', $episode->getTitle() ?: $episode->getNumber()));



            return $this->redirect($this->generateUrl('episodes_index'));

        }



        return $this->render('Episodes/create');

    }



    public function editAction($episodeID, Request $request)

    {

        if(!$episode = $this->episodes->find($episodeID))

        {

            throw $this->createNotFoundException();

        }



        if($request->isMethod('post'))

        {

            $new = $this->episodes->create($request->request->get('episode'));



            $this->episodes->update($new);

            $this->episodes->clearCache();



            $this->getSession()->getFlashBag()->add('msg', sprintf('Odcinek <strong>%s</strong> został zaktualizowany.', $new->getTitle() ?: $new->getNumber()));



            return $this->redirect($this->generateUrl('episodes_index'));

        }



        $episode = $this->episodes->create($episode);

        $category  = (new CategoryManager($this->getDatabase()))->find($episode->getCategoryId());



        $links = new LinkManager($this->getDatabase());

        $list = $links->findLinksBy(array('episode_id' => $episode->getId()), 'id DESC');



        return $this->render('Episodes/edit', array(

            'episode' => $episode,

            'category' => $category['name'],

            'list' => $list,

        ));

    }



    public function updateAction(Request $request)
    {
        $session = $this->getSession();
        if($request->request->has('chosen'))
        {
            $session->set('to_chosen', $request->request->get('chosen'));
        }
        if($request->request->has('action'))
        {
            $session->set('to_action', $request->request->get('action'));
        }
        if($request->request->has('confirm') && $session->has('to_chosen'))
        {
            if($session->get('to_action') == 'usun')
            {
                $toDelete = $session->get('to_chosen');
                if(!is_array($toDelete))
                {
                    $toDelete = array(0 => $toDelete);
                }
                foreach($toDelete as $id)
                {
                    $this->episodes->deleteWhere(array('id' => $id));
                }
                $this->episodes->clearCache();
                $session->remove('to_chosen');
                $session->remove('to_action');
                $session->getFlashBag()->add('msg', sprintf('Odcinki zostały usunięte (<strong>%s</strong>).', count($toDelete)));
            }
            elseif($session->get('to_action') == 'status-dzialajacy')
            {

                $chosen = $session->get('to_chosen');
                if(!is_array($chosen))
                    $chosen = array(0 => $chosen);
                foreach($chosen as $id)
                    $this->episodes->setAsAccepted($id);
                $this->episodes->clearCache();
                $session->remove('to_chosen');
                $session->remove('to_action');
                $session->getFlashBag()->add('msg', sprintf('Odcinki zostały ustawione jako działające (<strong>%s</strong>).', count($chosen)));
            }
            elseif($session->get('to_action') == 'status-uszkodzony')
            {
                $chosen = $session->get('to_chosen');
                if(!is_array($chosen))
                    $chosen = array(0 => $chosen);
                foreach($chosen as $id)
                    $this->episodes->setAsAccepted($id, 0);
                $this->episodes->clearCache();
                $session->remove('to_chosen');
                $session->remove('to_action');
                $session->getFlashBag()->add('msg', sprintf('Odcinki zostały ustawione jako uszkodzone (<strong>%s</strong>).', count($chosen)));
            }
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz wykonać tą akcję na zaznaczonych odcinkach (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_chosen'))),
                'action' => $this->generateUrl('episodes_update'),
            ));
        }
        return $this->redirect($this->generateUrl('episodes_index'));
    }



    public function deleteAction($episodeID, Request $request)

    {

        if(!$episode = $this->episodes->find($episodeID))

        {

            throw $this->createNotFoundException();

        }



        $episode = $this->episodes->create($episode);



        if($request->isMethod('post'))

        {

            if($episodeID !== $request->request->get('id'))

            {

                throw $this->createNotFoundException();

            }



            $this->episodes->delete($episode);

            $this->episodes->clearCache();



            $this->getSession()->getFlashBag()->add('msg', sprintf('Odcinek <strong>%s</strong> i powiązania został usunięty.', $episode->getTitle() ?: $episode->getNumber()));



            return $this->redirect($this->generateUrl('episodes_index'));

        }



        return $this->render('Episodes/delete', array(

            'episode' => $episode,

        ));

    }



    public function searchAction(Request $request)

    {

        if($request->isMethod('post'))

        {

            return $this->redirect($this->generateUrl('episodes_search', array(

                'name' => ($request->request->get('name') === '' ? null : $request->request->get('name')),

                'title' => ($request->request->get('title') === '' ? null : $request->request->get('title')),

                'number' => ($request->request->get('number') === '' ?  null : $request->request->get('number')),

                'filler' => ($request->request->get('filler') === '' ?  null : $request->request->get('filler')),

                'enabled' => ($request->request->get('enabled') === '' ?  null : $request->request->get('enabled')),

            )));

        }



        $query = array();



        if($request->query->has('name'))

        {

            $query['name LIKE'] = '%'.$request->query->get('name').'%';

        }



        if($request->query->has('title'))

        {

            $query['title LIKE'] = '%'.$request->query->get('title').'%';

        }



        if($request->query->has('number'))

        {

            $query['number'] = $request->query->get('number');

        }



        if($request->query->has('filler'))

        {

            $query['filler'] = $request->query->get('filler');

        }



        if($request->query->has('enabled'))

        {

            $query['enabled'] = $request->query->get('enabled');

        }



        if($request->query->has('category_id'))

        {

            $query['category_id'] = $request->query->get('category_id');

        }



        if(empty($query))

        {

            return $this->redirect($this->generateUrl('episodes_index'));

        }



        $list = $this->episodes->findList()->where($query);



        $pagination = new Pagination();

        $pagination->

        setBaseUrl($this->generateUrl('episodes_search', array(

            'name' => $request->query->get('name'),

            'title' => $request->query->get('title'),

            'number' => $request->query->get('number'),

            'filler' => $request->query->get('filler'),

            'enabled' => $request->query->get('enabled'),

            'category_id' => $request->query->get('category_id'),

        )))->

        setUrl($this->generateUrl('episodes_search', array(

            'page' => '_PAGE_',

            'name' => $request->query->get('name'),

            'title' => $request->query->get('title'),

            'number' => $request->query->get('number'),

            'filler' => $request->query->get('filler'),

            'enabled' => $request->query->get('enabled'),

            'category_id' => $request->query->get('category_id'),

        )))->

        setPerPage(20)->

        setRange(2)->

        setUrlNeedle('_PAGE_')->

        setTotalCount($total = $list->get()->rowCount())->

        setCurrentPage($request->query->get('page', 1));



        $list->offset($pagination->offset())->limit($pagination->limit());



        if(!isset($query['title']) && !isset($query['name']))

        {

            $list->order('id DESC');

        } else

        {

            $list->order('number DESC');

        }



        $list = $list->get();



        return $this->render('Episodes/search', array(

            'total' => $total,

            'list' => $list,

            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),

            'name' => $request->query->get('name'),

            'title' => $request->query->get('title'),

            'number' => $request->query->get('number'),

            'filler' => $request->query->get('filler'),

            'enabled' => $request->query->get('enabled'),

        ));

    }



    public function checkAction(Request $request)

    {

        if($request->isXmlHttpRequest() && $request->request->has('category') && $request->request->has('episode'))

        {

            $episode = $this->episodes->findOneBy(array(

                'category_id' => $request->request->get('category'),

                'number' => $request->request->get('episode'),

            ));



            if(false == $episode)

            {

                $response = array('status' => 'available');

            } else

            {

                $response = array('status' => 'unavailable');

            }



            return new JsonResponse($response);

        }



        throw $this->createNotFoundException();

    }



    public function listAction(Request $request)

    {

        if($request->isXmlHttpRequest() && $request->request->has('number') && $request->request->has('category'))

        {

            $list = $this->episodes->findBy(array(

                'category_id' => $request->request->get('category'),

                'number' => $request->request->get('number'),

            ), null, 8);



            $response = array();



            foreach($list as $row)

            {

                $response[] = array(

                    'id' => $row['id'],

                    'number' => $row['number'],

                    'name' => $row['number'].': '.($row['title'] ?: ' brak tytułu'),

                );

            }



            return new JsonResponse($response);

        }



        throw $this->createNotFoundException();

    }



    public function statsAction(Request $request)

    {

        if($request->isXmlHttpRequest())

        {

            return new JsonResponse($this->episodes->getStats());

        }



        throw $this->createNotFoundException();

    }



    public function reloadAction($episodeID, Request $request)

    {

        if(!$episode = $this->episodes->find($episodeID))

        {

            throw $this->createNotFoundException();

        }



        $this->episodes->update($this->episodes->create(array(

            'id' => $episodeID

        )));



        $this->episodes->clearCache();



        $this->getSession()->getFlashBag()->add('msg', sprintf('Odcinek <strong>%s: %s</strong> został odświeżony.', $episode['id'], $episode['title']));



        return $this->redirect($request->headers->get('referer', $this->generateUrl('episodes_index')));

    }

}