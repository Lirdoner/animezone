<?php

namespace Anime\Controller;


use Anime\Model\Category\CategoryManager;
use Anime\Model\Episode\EpisodeManager;
use Anime\Model\News\NewsManager;
use Sequence\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /** @var  \Anime\Model\Episode\EpisodeManager */
    protected $episodes;

    public function init()
    {
        $this->episodes = new EpisodeManager($this->getDatabase(), $this->getCache());
    }

    public function indexAction($lang)
    {
        $categories = new CategoryManager($this->getDatabase(), $this->getCache());

        $newsManager = new NewsManager($this->getDatabase(), $this->getCache());
        $news = $newsManager->getSidebar(5);

        $session = $this->getSession();

        if($this->getUser()->isUser())
        {
            //watching
            /** @var \Anime\Model\Watch\WatchBag $userWatching */
            $watching = $session->get('watched');
        } else
        {
            $watching = false;
        }

        return $this->render('Home/index', array(
            'latest_episodes' => $this->episodes->getLatest(60, 'all' == $lang),
            'newest_series' => $categories->getLastSeries(),
            'sidebar' => $categories->getSidebar(),
            'lang' => 'pl' == $lang ? null : 'pl',
            'news' => $news,
            'watching' => $watching,
        ));
    }
	
    public function rssAction($lang)
    {
        if('all' == $lang)
        {
            $items = $this->episodes->getLatest(15);
        } else
        {
            $items = $this->episodes->getLatest(15, false);
        }

        $date = null;

        if(isset($items[0]))
        {
            $date = new \DateTime($items[0]['date_add']);
            $date = $date->format('D, d M Y H:i:s').' GMT';
        }

        return new Response($this->renderView('Home/rss', array(
            'items' => $items,
            'date' => $date,
            'lang' => $lang,
        )), 200, array(
            'Content-Type' => 'text/xml; charset=UTF-8',
        ));
    }
} 