<?php


namespace Admin\Controller;


use Anime\Model\Ads\Ads;
use Anime\Model\Ads\AdsManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class AdsController extends Controller
{
    /** @var  \Anime\Model\Ads\AdsManager */
    protected $ads;

    public function init()
    {
        /** @var \Sequence\Cache\Cache $cache */
        $cache = $this->get('front_cache');

        $this->ads = new AdsManager($this->getDatabase(), $cache);
    }

    public function indexAction(Request $request)
    {
        $list = $this->ads->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('ads_index'))->
            setUrl($this->generateUrl('ads_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Ads/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $ad = new Ads($request->request->get('ads'));

            $this->ads->update($ad);
            $this->ads->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('Reklama <strong>%s</strong> została utworzona.', $ad->getAlias()));

            return $this->redirect($this->generateUrl('ads_index'));
        }

        return $this->render('Ads/create');
    }

    public function editAction($adID, Request $request)
    {
        if(!$ad = $this->ads->findOneBy(array('id' => $adID)))
        {
            throw $this->createNotFoundException();
        }

        $ad = new Ads($ad);

        if($request->isMethod('post'))
        {
            $new = new Ads($request->request->get('ads'));

            $this->ads->update($new);
            $this->ads->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('reklama <strong>%s</strong> została zaktualizowana na <strong>%s</strong>.', $ad->getAlias(), $new->getAlias()));

            return $this->redirect($this->generateUrl('ads_index'));
        }

        return $this->render('Ads/edit', array(
            'ad' => $ad,
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
                $this->ads->delete(new Ads(array('id' => $id)));
            }

            $this->ads->clearCache();

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Reklamy zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone reklamy (<strong>%s</strong>) wraz z kodami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('ads_update'),
            ));
        }

        return $this->redirect($this->generateUrl('ads_index'));
    }

    public function deleteAction($adID, Request $request)
    {
        if(!$ad = $this->ads->findOneBy(array('id' => $adID)))
        {
            throw $this->createNotFoundException();
        }

        $ad = new Ads($ad);

        if($request->isMethod('post'))
        {
            if($adID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->ads->delete($ad);
            $this->ads->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('Reklama <strong>%s</strong> i powiązania została usunięta.', $ad->getAlias()));

            return $this->redirect($this->generateUrl('ads_index'));
        }

        return $this->render('Ads/delete', array(
            'ad' => $ad,
        ));
    }
} 