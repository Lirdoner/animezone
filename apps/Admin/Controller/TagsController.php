<?php


namespace Admin\Controller;


use Anime\Model\News\NewsTags;
use Anime\Model\News\NewsTagsManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class TagsController extends Controller
{
    /** @var  \Anime\Model\News\NewsTagsManager */
    protected $tags;

    public function init()
    {
        $this->tags = new NewsTagsManager($this->getDatabase());
    }

    public function indexAction(Request $request)
    {
        $list = $this->tags->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('tags_index'))->
            setUrl($this->generateUrl('tags_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Tags/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $tag = new NewsTags($request->request->get('tag'));

            $this->tags->update($tag);
            $this->getSession()->getFlashBag()->add('msg', sprintf('Tag <strong>%s</strong> został utworzony.', $tag->getName()));

            return $this->redirect($this->generateUrl('tags_index'));
        }

        return $this->render('Tags/create');
    }

    public function editAction($tagID, Request $request)
    {
        if(!$tag = $this->tags->findOneBy(array('id' => $tagID)))
        {
            throw $this->createNotFoundException();
        }

        $tag = new NewsTags($tag);

        if($request->isMethod('post'))
        {
            $new = new NewsTags($request->request->get('tag'));

            $this->tags->update($new);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Tag <strong>%s</strong> został zaktualizowany na <strong>%s</strong>.', $tag->getName(), $new->getName()));

            return $this->redirect($this->generateUrl('tags_index'));
        }

        return $this->render('Tags/edit', array(
            'tag' => $tag,
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
                $this->tags->delete(new NewsTags(array('id' => $id)));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Tagi zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone tagi (<strong>%s</strong>) wraz z powiązaniami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('tags_update'),
            ));
        }

        return $this->redirect($this->generateUrl('tags_index'));
    }

    public function deleteAction($tagID, Request $request)
    {
        if(!$tag = $this->tags->findOneBy(array('id' => $tagID)))
        {
            throw $this->createNotFoundException();
        }

        $tag = new NewsTags($tag);

        if($request->isMethod('post'))
        {
            if($tagID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->tags->delete($tag);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Tag <strong>%s</strong> i powiązania został usunięty.', $tag->getName()));

            return $this->redirect($this->generateUrl('tags_index'));
        }

        return $this->render('Tags/delete', array(
            'tag' => $tag,
        ));
    }
} 