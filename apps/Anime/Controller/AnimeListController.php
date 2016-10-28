<?php


namespace Anime\Controller;


use Anime\Model\Category\Category;
use Anime\Model\Category\CategoryManager;
use Anime\Model\Category\RatingEntity;
use Anime\Model\Category\RatingRepository;
use Anime\Model\Faq\FaqManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class AnimeListController extends Controller
{
    /** @var  \Anime\Model\Category\CategoryManager */
    protected $categoryManager;

    /** @var  array */
    protected $sidebar;

    public function init()
    {
        $this->categoryManager = new CategoryManager($this->getDatabase());
        $this->sidebar = array(
            'ocen' => 'Ocen',
            'wyswietlen' => 'Wyświetleń',
            'fanow' => 'Fanów',
        );

        $this->get('templating')->addGlobal('rankingRoutes', array(
            'anime' => $this->generateUrl('anime_watch_ranking', array('type' => 'anime')),
            'ocen' => $this->generateUrl('anime_rate_ranking'),
            'wyswietlen' => $this->generateUrl('anime_views_ranking'),
            'fanow' => $this->generateUrl('anime_fan_ranking'),
            'filmow' => $this->generateUrl('anime_watch_ranking', array('type' => 'filmow')),
        ));
    }

    public function typeAction($type, $letter, Request $request)
    {
        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('anime_list_by_type', array('type' => $type, 'letter' => $letter)))->
            setUrl($this->generateUrl('anime_list_by_type', array('type' => $type, 'letter' => $letter, 'page' => '_PAGE_')))->
            setPerPage(50)->
            setRange(1)->
            setUrlNeedle('_PAGE_');

        $list = $this->categoryManager->findListBy(array(
            'letter' => $letter,
            'release'.('filmy' == $type ? null : ' !=') => Category::RELEASE_MOVIE,
        ), 'name ASC');

        $pagination->setTotalCount($list->get()->rowCount());

        try
        {
            $pagination->setCurrentPage($request->query->get('page', 1));
        } catch(\InvalidArgumentException $e)
        {
            throw $this->createNotFoundException();
        }

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->get();

        $letter_list = range('A', 'Z');
        $letter_list[] = 0;
        sort($letter_list, SORT_STRING);

        $faqManager = new FaqManager($this->getDatabase());

        return $this->render('AnimeList/type', array(
            'list' => $list,
            'type' => $type,
            'letter' => $letter,
            'letter_list' => $letter_list,
            'pagination' => $pagination,
            'sidebar' => $faqManager->findAll(),
        ));
    }

    public function statusAction($status)
    {
        $types = array(
            'nadchodzace' => array(
                'status' => Category::STATUS_COMING,
                'title' => 'Nadchodzące',
            ),
            'zakonczone' => array(
                'status' => Category::STATUS_RECENTLY_ENDED,
                'title' => 'Niedawno zakończone',
            ),
        );

        $list = $this->categoryManager->findBy(array('status' => $types[$status]['status']), 'name ASC');

        $faqManager = new FaqManager($this->getDatabase());

        return $this->render('AnimeList/status', array(
            'list' => $list,
            'status' => $status,
            'title' => $types[$status]['title'],
            'sidebar' => $faqManager->findAll(),
        ));
    }

    public function watchRankingAction($type, $watch, Request $request)
    {
        $watching = array(
            'watched' => array(
                'nav' => 'Obejrzanych',
                'title' => 'Obejrzało'
            ),
            'watching' => array(
                'nav' => 'Oglądanych',
                'title' => 'Ogląda',
            ),
            'plans' => array(
                'nav' => 'Planowanych',
                'title' => 'Planuje',
            ),
            'stopped' => array(
                'nav' => 'Wstrzymanych',
                'title' => 'Wstrzymało',
            ),
            'abandoned' => array(
                'nav' => 'Porzuconych',
                'title' => 'Porzuciło',
            ),
        );

        $repository = new RatingRepository($request);
        $repository->add(new RatingEntity('views', 'Wyświetleń'));
        $repository->add(new RatingEntity($watch, $watching[$watch]['title']));
        $repository->setCurrent($request->query->has('views') ? 'views' : $watch);

        if('anime' == $type)
        {
            $condition = array($watch.' >' => 0, 'release !=' => Category::RELEASE_MOVIE);
        } else
        {
            $condition = array($watch.' >' => 0, 'release' => Category::RELEASE_MOVIE);
        }

        $ranking = $this->categoryManager->findBy($condition, $repository->getCurrent()->getColumn().' '.($repository->getOrder('sql_desc')), 50);

        return $this->render('AnimeList/ranking/watch', array(
            'sidebar' => $this->sidebar,
            'watching' => $watching,
            'watch' => $watch,
            'type' => $type,
            'repository' => $repository,
            'ranking' => $ranking,
        ));
    }

    public function rateRankingAction($type, Request $request)
    {
        $submenu = array(
            'anime' => 'Anime',
            'filmy' => 'Filmy',
        );

        $repository = new RatingRepository($request);
        $repository->add(new RatingEntity('rating_avg', 'Ocena'));
        $repository->add(new RatingEntity('rating_count', 'Głosy'));
        $repository->setCurrent($request->query->has('rating_avg') ? 'rating_avg' : 'rating_count');

        if('anime' == $type)
        {
            $criteria = array('rating_count >' => 30, 'release !=' => Category::RELEASE_MOVIE);
        } else
        {
            $criteria = array('rating_count >' => 30, 'release' => Category::RELEASE_MOVIE);
        }

        $ranking = $this->categoryManager->findBy($criteria, $repository->getCurrent()->getColumn().' '.($repository->getOrder('sql_desc')), 50);

        return $this->render('AnimeList/ranking/rate', array(
            'sidebar' => $this->sidebar,
            'submenu' => $submenu,
            'type' => $type,
            'repository' => $repository,
            'ranking' => $ranking,
        ));
    }

    public function fansRankingAction($type, Request $request)
    {
        $submenu = array(
            'anime' => 'Anime',
            'filmy' => 'Filmy',
        );

        $repository = new RatingRepository($request);
        $repository->add(new RatingEntity('fans', 'Ilość Fanów'));
        $repository->setCurrent('fans');

        if('anime' == $type)
        {
            $criteria = array('fans >' => 0, 'release !=' => Category::RELEASE_MOVIE);
        } else
        {
            $criteria = array('fans >' => 0, 'release' => Category::RELEASE_MOVIE);
        }

        $ranking = $this->categoryManager->findBy($criteria, $repository->getCurrent()->getColumn().' '.($repository->getOrder('sql_desc')), 50);

        return $this->render('AnimeList/ranking/fans', array(
            'sidebar' => $this->sidebar,
            'submenu' => $submenu,
            'type' => $type,
            'repository' => $repository,
            'ranking' => $ranking,
        ));
    }

    public function viewsRankingAction($type, Request $request)
    {
        $submenu = array(
            'anime' => 'Anime',
            'filmy' => 'Filmy',
        );

        $repository = new RatingRepository($request);
        $repository->add(new RatingEntity('views', 'Wyświetleń'));
        $repository->setCurrent('views');

        if('anime' == $type)
        {
            $criteria = array('views >' => 0, 'release !=' => Category::RELEASE_MOVIE);
        } else
        {
            $criteria = array('views >' => 0, 'release' => Category::RELEASE_MOVIE);
        }

        $ranking = $this->categoryManager->findBy($criteria, $repository->getCurrent()->getColumn().' '.($repository->getOrder('sql_desc')), 50);

        return $this->render('AnimeList/ranking/views', array(
            'sidebar' => $this->sidebar,
            'submenu' => $submenu,
            'type' => $type,
            'repository' => $repository,
            'ranking' => $ranking,
        ));
    }

    public function seasonAction($year, $season)
    {
        $currentSeason = array(
            'zima' => 1,
            'wiosna' => 2,
            'lato' => 3,
            'jesien' => 4,
        );

        if('current' == $year)
        {
            $year = date('Y');
        }

        if('current' == $season)
        {
            $season = date('n');

            if($season >= 4 && $season <= 6)
            {
                $season = 'wiosna';
            } elseif($season >= 7 && $season <= 9)
            {
                $season = 'lato';
            } elseif($season >= 10 && $season <= 12)
            {
                $season = 'jesien';
            } else
            {
                $season = 'zima';
            }
        }

        if(!$this->categoryManager->findOneBy(array('year' => $year)))
        {
            throw $this->createNotFoundException();
        }

        $nav = array(
            'prev' => array(),
            'next' => array(),
        );

        if(1 == $currentSeason[$season])
        {
            $prev = $this->categoryManager->getPrevSeason($year);
            $_season = array_flip($currentSeason);

            $nav['prev'] = array(
                'year' => ($prev ? $prev['year'] : $prev),
                'season' => $_season[4],
            );

            $nav['next'] = array(
                'year' => $year,
                'season' => $_season[2],
            );
        } elseif(2 == $currentSeason[$season])
        {
            $_season = array_flip($currentSeason);

            $nav['prev'] = array(
                'year' => $year,
                'season' => $_season[1],
            );

            $nav['next'] = array(
                'year' => $year,
                'season' => $_season[3],
            );
        } elseif(3 == $currentSeason[$season])
        {
            $_season = array_flip($currentSeason);

            $nav['prev'] = array(
                'year' => $year,
                'season' => $_season[2],
            );

            $nav['next'] = array(
                'year' => $year,
                'season' => $_season[4],
            );
        } elseif(4 == $currentSeason[$season])
        {
            $next = $this->categoryManager->getNextSeason($year);
            $_season = array_flip($currentSeason);

            $nav['prev'] = array(
                'year' => $year,
                'season' => $_season[3],
            );

            $nav['next'] = array(
                'year' => ($next ? $next['year'] : $next),
                'season' => $_season[1],
            );
        }

        $list = $this->categoryManager->findBy(array(
            'year' => $year,
            'season' => $currentSeason[$season],
        ), 'name ASC');

        $faqManager = new FaqManager($this->getDatabase());

        return $this->render('AnimeList/season', array(
            'sidebar' => $faqManager->findAll(),
            'list' => $list,
            'year' => $year,
            'season' => $season,
            'nav' => $nav,
        ));
    }
} 