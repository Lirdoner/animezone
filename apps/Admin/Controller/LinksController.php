<?php


namespace Admin\Controller;


use Anime\Model\Category\CategoryManager;
use Anime\Model\Episode\EpisodeManager;
use Anime\Model\Link\LinkManager;
use Anime\Model\Server\ServerManager;
use Sequence\Controller;
use Sequence\Database\Database;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LinksController extends Controller
{
    /** @var  \Anime\Model\Link\LinkManager */
    protected $links;

    /** @var  \Anime\Model\Episode\EpisodeManager */
    protected $episodes;

    /** @var  \Anime\Model\Server\ServerManager */
    protected $servers;

    /** @var  \Anime\Model\Category\CategoryManager */
    protected $categories;

    public function init()
    {
        /** @var \Sequence\Cache\Cache $cache */
        $cache = $this->get('front_cache');

        $this->links = new LinkManager($this->getDatabase(), $cache);
        $this->episodes = new EpisodeManager($this->getDatabase(), $cache);
        $this->servers = new ServerManager($this->getDatabase());
        $this->categories = new CategoryManager($this->getDatabase());

        $this->get('templating')->addGlobal('servers', $this->servers->findAll('name ASC'));
        $this->get('templating')->addGlobal('languages', $this->links->create()->getLanguages());
    }

    public function indexAction(Request $request)
    {
        $list = $this->links->
            findList('l.*, s.name, e.number, e.category_id, c.name AS anime')->
            join('episodes', 'l.episode_id=e.id', 'e')->
            join('categories', 'c.id=e.category_id', 'c');

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('links_index'))->
            setUrl($this->generateUrl('links_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Links/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $link = $this->links->create($request->request->get('link'));

            $this->links->update($link);
            $this->links->clearCache();

            $this->getSession()->getFlashBag()->add('msg', 'Link  został utworzony.');

            $repeat = array();

            if($request->request->has('repeat'))
            {
                $repeat['categoryName'] = $request->request->get('categoryName');
                $repeat['categoryId'] = $request->request->get('categoryId');
                $repeat['episodeNumber'] = $request->request->get('episodeNumber');
                $repeat['episodeId'] = $link->getEpisodeId();

                return $this->redirect($this->generateUrl('links_create', $repeat));
            }

            return $this->redirect($this->generateUrl('links_index'));
        }

        return $this->render('Links/create', array(
            'categoryName' => $request->query->get('categoryName'),
            'categoryId' => $request->query->get('categoryId'),
            'episodeNumber' => $request->query->get('episodeNumber'),
            'episodeId' => $request->query->get('episodeId'),
        ));
    }

    public function editAction($linkID, Request $request)
    {
        if(!$link = $this->links->find($linkID))
        {
            throw $this->createNotFoundException();
        }

        if($request->isMethod('post'))
        {
            $new = $this->links->create($request->request->get('link'));

            $this->links->update($new);
            $this->links->clearCache();

            $this->getSession()->getFlashBag()->add('msg', 'Link został zaktualizowany.');

            return $this->redirect($this->generateUrl('links_index'));
        }

        $link = $this->links->create($link);
        $episode = $this->episodes->find($link->getEpisodeId());
        $category = $this->categories->find($episode['category_id']);

        return $this->render('Links/edit', array(
            'link' => $link,
            'episode' => $episode,
            'category' => $category,
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
                $this->links->deleteWhere(array('id' => $id));
            }

            $this->links->clearCache();

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Linki zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone linki (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('links_update'),
            ));
        }

        return $this->redirect($this->generateUrl('links_index'));
    }

    public function deleteAction($linkID, Request $request)
    {
        if(!$link = $this->links->find($linkID))
        {
            throw $this->createNotFoundException();
        }

        $link = $this->links->create($link);

        if($request->isMethod('post'))
        {
            if($linkID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->links->delete($link);
            $this->links->clearCache();

            $this->getSession()->getFlashBag()->add('msg', 'Link i powiązania został usunięty.');

            return $this->redirect($this->generateUrl('links_index'));
        }

        return $this->render('Links/delete', array(
            'link' => $link,
        ));
    }

    public function searchAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            return $this->redirect($this->generateUrl('links_search', array(
                'name' => ($request->request->get('name') === '' ? null : $request->request->get('name')),
                'title' => ($request->request->get('title') === '' ? null : $request->request->get('title')),
                'number' => ($request->request->get('number') === '' ?  null : $request->request->get('number')),
                'server_id' => ($request->request->get('server_id') === '' ?  null : $request->request->get('server_id')),
                'lang_id' => ($request->request->get('lang_id') === '' ?  null : $request->request->get('lang_id')),
            )));
        }

        $query = array();

        if($request->query->has('name'))
        {
            $query[] = 'c.name LIKE "%'.$request->query->get('name').'%"';
        }

        if($request->query->has('title'))
        {
            $query[] = 'e.title LIKE "%'.$request->query->get('title').'%"';
        }

        if($request->query->has('number'))
        {
            $query[] = 'e.number='.$request->query->get('number');
        }

        if($request->query->has('server_id'))
        {
            $query[] = 'l.server_id='.$request->query->get('server_id');
        }

        if($request->query->has('lang_id'))
        {
            $query[] = 'l.lang_id='.$request->query->get('lang_id');
        }

        if(empty($query))
        {
            return $this->redirect($this->generateUrl('links_index'));
        }

        $list = $this->links->
            findList('l.*, s.name, e.number, e.category_id, c.name AS anime')->
            join('episodes', 'l.episode_id=e.id', 'e')->
            join('categories', 'c.id=e.category_id', 'c')->
            where(implode(' '.Database::SQL_AND.' ', $query));

        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('links_search', array(
                'name' => $request->query->get('name'),
                'title' => $request->query->get('title'),
                'number' => $request->query->get('number'),
                'server_id' => $request->query->get('server_id'),
                'lang_id' => $request->query->get('lang_id'),
            )))->
            setUrl($this->generateUrl('links_search', array(
                'page' => '_PAGE_',
                'name' => $request->query->get('name'),
                'title' => $request->query->get('title'),
                'number' => $request->query->get('number'),
                'server_id' => $request->query->get('server_id'),
                'lang_id' => $request->query->get('lang_id'),
            )))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC');

        $list = $list->get();

        return $this->render('Links/search', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
            'name' => $request->query->get('name'),
            'title' => $request->query->get('title'),
            'number' => $request->query->get('number'),
            'server_id' => $request->query->get('server_id'),
            'lang_id' => $request->query->get('lang_id'),
        ));
    }

    public function clearAction(Request $request)
    {
        $this->episodes->clearCache();

        $this->getSession()->getFlashBag()->add('msg', 'Cache zostało wyczyszczone.');

        return $this->redirect($request->headers->get('referer', $this->generateUrl('links_index')));
    }

    public function statsAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            return new JsonResponse($this->links->getStats());
        }

        throw $this->createNotFoundException();
    }
} 