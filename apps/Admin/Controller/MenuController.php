<?php


namespace Admin\Controller;


use Anime\Model\Menu\MenuManager;
use Sequence\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends Controller
{
    /** @var  \Anime\Model\Menu\MenuManager */
    protected $menu;

    public function init()
    {
        /** @var \Sequence\Cache\Cache $cache */
        $cache = $this->get('front_cache');

        $this->menu = new MenuManager($this->getDatabase(), $cache);
    }

    public function indexAction()
    {
        return $this->render('Menu/index', array(
            'list' => $this->menu->getSorted(),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $menu = $this->menu->create($request->request->get('menu'));

            $this->menu->update($menu);
            $this->menu->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('Link w menu <strong>%s</strong> został utworzony.', $menu->getName()));

            return $this->redirect($this->generateUrl('menu_index'));
        }

        return $this->render('Menu/create', array(
            'list' => $this->menu->findBy(array('parent_id' => 0)),
        ));
    }

    public function editAction($menuID, Request $request)
    {
        if(!$menu = $this->menu->findOneBy(array('id' => $menuID)))
        {
            throw $this->createNotFoundException();
        }

        $menu = $this->menu->create($menu);

        if(!$menu->getParentId())
        {
            $submenu = $this->menu->findBy(array('parent_id' => $menu->getId()), 'position ASC');
        } else
        {
            $submenu = array();
        }

        if($request->isMethod('post'))
        {
            $new = $this->menu->create($request->request->get('menu'));

            $this->menu->update($new);
            $this->menu->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('Link <strong>%s</strong> został zaktualizowany na <strong>%s</strong>.', $menu->getName(), $new->getName()));

            return $this->redirect($this->generateUrl('menu_index'));
        }

        return $this->render('Menu/edit', array(
            'list' => $this->menu->findBy(array('parent_id' => 0)),
            'menu' => $menu,
            'submenu' => $submenu,
        ));
    }

    public function updateAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->request->has('items'))
        {
            foreach($request->request->get('items') as $item)
            {
                $this->menu->update($this->menu->create(array(
                    'id' => $item['id'],
                    'position' => $item['position'],
                )));
            }

            $this->menu->clearCache();

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    public function deleteAction($menuID, Request $request)
    {
        if(!$menu = $this->menu->findOneBy(array('id' => $menuID)))
        {
            throw $this->createNotFoundException();
        }

        $menu = $this->menu->create($menu);

        if($request->isMethod('post'))
        {
            if($menuID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->menu->delete($menu);
            $this->menu->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('Link <strong>%s</strong> został usunięty.', $menu->getName()));

            return $this->redirect($this->generateUrl('menu_index'));
        }

        return $this->render('Menu/delete', array(
            'menu' => $menu,
        ));
    }
} 