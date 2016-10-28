<?php


namespace Admin\Controller;


use Anime\Model\News\News;
use Anime\Model\News\NewsManager;
use Anime\Model\News\NewsTagsManager;
use Anime\Model\News\NewsWithTagsManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    /** @var  \Anime\Model\News\NewsManager */
    protected $news;

    /** @var  \Anime\Model\News\NewsTagsManager */
    protected $tags;

    /** @var  \Anime\Model\News\NewsWithTagsManager */
    protected $newsWithTags;

    public function init()
    {
        /** @var \Sequence\Cache\Cache $cache */
        $cache = $this->get('front_cache');

        $this->news = new NewsManager($this->getDatabase(), $cache);
        $this->tags = new NewsTagsManager($this->getDatabase());
        $this->newsWithTags = new NewsWithTagsManager($this->getDatabase());
    }

    public function indexAction(Request $request)
    {
        $list = $this->news->findList();

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('news_index'))->
            setUrl($this->generateUrl('news_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('date DESC')->get();

        return $this->render('News/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $news = $this->news->create($request->request->get('news'));

            //check if image is send as file
            if($news->getImage() == 'file')
            {
                if($file = $request->files->get('image', false))
                {
                    $file->move($this->get('config')->anime->get('category_images'), $file->getClientOriginalName());
                    $news->setImage($file->getClientOriginalName());
                } else
                {
                    $this->getSession()->getFlashBag()->add('msg', array(
                        'danger' => 'Wystąpił błąd podczas odczytu przesłanego obrazu. Spróbuj ponownie.'
                    ));

                    return $this->redirect($this->generateUrl('news_create'));
                }
            }

            $news->setViews(0);
            if(!$news->getComments())
            {
                $news->setComments(0);
            }

            $this->news->update($news);
            $this->news->clearCache();

            $news->setId($this->getDatabase()->lastInsertId());

            //create connections for tags
            foreach($request->request->get('tags', array()) as $tag)
            {
                $this->newsWithTags->update($this->newsWithTags->create(array(
                    'news_id' => $news->getId(),
                    'tag_id' => $tag,
                )));
            }

            $this->getSession()->getFlashBag()->add('msg', sprintf('News <strong>%s</strong> został utworzony.', $news->getTitle()));

            return $this->redirect($this->generateUrl('news_index'));
        }

        return $this->render('News/create', array(
            'tags' => $this->tags->findAll('name'),
        ));
    }

    public function editAction($newsID, Request $request)
    {
        if(!$news = $this->news->findOneBy(array('id' => $newsID)))
        {
            throw $this->createNotFoundException();
        }

        $news = $this->news->create($news);

        $tags = array();
        foreach($this->newsWithTags->findBy(array('news_id' => $news->getId())) as $row)
        {
            $tags[$row['tag_id']] = $row['id'];
        }

        if($request->isMethod('post'))
        {
            $news = $this->news->create($request->request->get('news'));

            //check if image is send as file
            if($news->getImage() == 'file')
            {
                if($file = $request->files->get('image', false))
                {
                    $file->move($this->get('config')->anime->get('category_images'), $file->getClientOriginalName());
                    $news->setImage($file->getClientOriginalName());
                } else
                {
                    $this->getSession()->getFlashBag()->add('msg', 'Wystąpił błąd podczas odczytu przesłanego obrazu. Spróbuj ponownie.');

                    return $this->redirect($this->generateUrl('news_create'));
                }
            }

            $this->news->update($news);
            $this->news->clearCache();

            //update connections for tags
            foreach(array_diff_key($tags, $request->request->get('tags')) as $i => $id)
            {
                $this->newsWithTags->deleteWhere(array('id' => $id));
            }

            foreach($request->request->get('tags') as $id => $v)
            {
                if(empty($v))
                {
                    $this->newsWithTags->update($this->newsWithTags->create(array(
                        'news_id' => $news->getId(),
                        'tag_id' => $id,
                    )));
                }
            }

            $this->getSession()->getFlashBag()->add('msg', sprintf('News <strong>%s</strong> został zaktualizowany.', $news->getTitle()));

            return $this->redirect($this->generateUrl('news_index'));
        }

        return $this->render('News/edit', array(
            'news' => $news,
            'tags' => $this->tags->findAll('name'),
            '_tags' => $tags,
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
                $this->news->delete(new News(array('id' => $id)));
            }

            $this->news->clearCache();

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Newst zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone newsy (<strong>%s</strong>) wraz z komentarzami?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('news_update'),
            ));
        }

        return $this->redirect($this->generateUrl('news_index'));
    }

    public function deleteAction($newsID, Request $request)
    {
        if(!$news = $this->news->findOneBy(array('id' => $newsID)))
        {
            throw $this->createNotFoundException();
        }

        $news = new News($news);

        if($request->isMethod('post'))
        {
            if($newsID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->news->delete($news);
            $this->news->clearCache();

            $this->getSession()->getFlashBag()->add('msg', sprintf('News <strong>%s</strong> wraz z komentarzami, został usunięty.', $news->getTitle()));

            return $this->redirect($this->generateUrl('news_index'));
        }

        return $this->render('News/delete', array(
            'news' => $news,
        ));
    }

    public function aliasAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->request->has('query'))
        {
            $category = $this->news->findOneBy(array('alias' => $request->request->get('query')));

            $response = array();

            if(false !== $category)
            {
                $response['id'] = $category['id'];
                $response['title'] = $category['title'];
            }

            return new JsonResponse($response);
        }

        throw $this->createNotFoundException();
    }
} 