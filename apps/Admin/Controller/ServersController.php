<?php


namespace Admin\Controller;


use Anime\Model\Episode\EpisodeManager;
use Anime\Model\Link\LinkManager;
use Anime\Model\Server\ServerManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServersController extends Controller
{
    /** @var  \Anime\Model\Server\ServerManager */
    protected $servers;

    /** @var  \Anime\Model\Episode\EpisodeManager */
    protected $episodes;

    public function init()
    {
        /** @var \Sequence\Cache\Cache $cache */
        $cache = $this->get('front_cache');

        $this->servers = new ServerManager($this->getDatabase());
        $this->episodes = new EpisodeManager($this->getDatabase(), $cache);
    }

    public function indexAction(Request $request)
    {
        $list = $this->servers->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('servers_index'))->
            setUrl($this->generateUrl('servers_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('name ASC')->get();

        return $this->render('Servers/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $server = $this->servers->create($request->request->get('server'));

            $this->servers->update($server);
            $this->getSession()->getFlashBag()->add('msg', sprintf('Serwer <strong>%s</strong> został utworzony.', $server->getName()));

            return $this->redirect($this->generateUrl('servers_index'));
        }

        return $this->render('Servers/create');
    }

    public function editAction($serverID, Request $request)
    {
        if(!$server = $this->servers->find($serverID))
        {
            throw $this->createNotFoundException();
        }

        $server = $this->servers->create($server);

        if($request->isMethod('post'))
        {
            $new = $this->servers->create($request->request->get('server'));

            $this->servers->update($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Serwer <strong>%s</strong> został zaktualizowany na <strong>%s</strong>.', $server->getName(), $new->getName()));

            return $this->redirect($this->generateUrl('servers_index'));
        }

        return $this->render('Servers/edit', array(
            'server' => $server,
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
                $this->servers->deleteWhere(array('id' => $id));
            }

            $this->episodes->clearCache();

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Serwer(y) zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone serwery (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('servers_update'),
            ));
        }

        return $this->redirect($this->generateUrl('servers_index'));
    }

    public function deleteAction($serverID, Request $request)
    {
        if(!$server = $this->servers->find($serverID))
        {
            throw $this->createNotFoundException();
        }

        $server = $this->servers->create($server);

        if($request->isMethod('post'))
        {
            if($serverID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->servers->delete($server);
            $this->episodes->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('Serwer <strong>%s</strong> i powiązania został usunięty.', $server->getName()));

            return $this->redirect($this->generateUrl('servers_index'));
        }

        return $this->render('Servers/delete', array(
            'server' => $server,
        ));
    }

    public function hintAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw $this->createNotFoundException();
        }

        if(!$server = $this->servers->find($request->get('server_id')))
        {
            throw $this->createNotFoundException();
        }

        $server = $this->servers->create($server);

        $linkManager = new LinkManager($this->getDatabase());
        $link = $linkManager->create($linkManager->findOneBy(array('server_id' => $server->getId())));

        $code = '<kbd>{'.$link->getCode().'}</kbd>';

        $response = htmlspecialchars($server->getTemplate());
        $response = str_replace($server->getTemplateSearchPattern(), $code, $response);

        return new Response($response);
    }
} 