<?php


namespace Anime\Controller;


use Anime\Model\Faq\FaqManager;
use Anime\Model\News\NewsManager;
use Anime\Model\News\NewsTagsManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    /** @var  \Anime\Model\News\NewsManager */
    private $news;

    /** @var  \Anime\Model\Faq\FaqManager */
    private $faq;

    /** @var  \Anime\Model\News\NewsTagsManager */
    private $tags;

    public function init()
    {
        $this->news = new NewsManager($this->getDatabase());
        $this->tags = new NewsTagsManager($this->getDatabase());
        $this->faq = new FaqManager($this->getDatabase());

        $this->get('templating')->addGlobal('sidebar', $this->faq->findAll());
    }

    public function indexAction($page)
    {
        $latest = $this->news->findList();

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('news'))->
            setUrl($this->generateUrl('news', array('page' => '_PAGE_')))->
            setPerPage(10)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($latest->get()->rowCount())->
            setCurrentPage($page);

        $latest = $latest->
            offset($pagination->offset())->
            limit($pagination->limit())->
            order('date DESC')->
            get();

        return $this->render('News/index', array(
            'latest' => $latest,
            'tags' => $this->tags,
            'pagination' => $pagination->getHtml('margin:0', 'pagination-lg'),
        ));
    }

    public function showAction($slug)
    {
        $column = preg_match('/^\d+$/', $slug) ? 'id' : 'alias';

        if(false == $news = $this->news->findOneBy(array($column => $slug)))
        {
            throw $this->createNotFoundException();
        }

        $news = $this->news->create($news);
        $this->news->updateView($news);
        $tags = $this->tags->findForNews($news->getId());

        return $this->render('News/show', array(
            'news' => $news,
            'tags' => $tags,
        ));
    }

    public function tagsAction($tagID, Request $request)
    {
        if(false == $tag = $this->tags->find($tagID))
        {
            throw $this->createNotFoundException();
        }

        $list = $this->news->findList()->
            join('news_with_tags', 't.news_id=n.id', 't')->
            where(array('tag_id' => $tagID));

        $total = $list->get()->rowCount();

        if(!$total)
        {
            throw $this->createNotFoundException();
        }

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('news_tags', array('tagID' => $tagID)))->
            setUrl($this->generateUrl('news_tags', array('tagID' => $tagID, 'page' => '_PAGE_')))->
            setPerPage(10)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total)->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->
            offset($pagination->offset())->
            limit($pagination->limit())->
            order('date DESC')->
            get();

        return $this->render('News/tags', array(
            'list' => $list,
            'pagination' => $pagination->getHtml('margin:0', 'pagination-lg'),
        ));
    }
} 