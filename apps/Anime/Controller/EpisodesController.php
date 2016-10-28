<?php


namespace Anime\Controller;


use Anime\Model\Category\CategoryManager;
use Anime\Model\Episode\EpisodeManager;
use Anime\Model\Faq\FaqManager;
use Anime\Model\Favorite\FavoriteManager;
use Anime\Model\Link\Link;
use Anime\Model\Link\LinkManager;
use Anime\Model\Rating\RatingManager;
use Anime\Model\Server\ServerManager;
use Anime\Model\Species\SpeciesManager;
use Anime\Model\SubmittedEpisode\SubmittedEpisodeManager;
use Anime\Model\Topics\TopicsManager;
use Anime\Model\Type\TypeManager;
use Anime\Model\Watch\WatchBag;
use Anime\Model\Watch\WatchManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sequence\Controller;
use Sequence\Validator\StringLength;

class EpisodesController extends Controller
{
    /** @var  \Anime\Model\Episode\EpisodeManager */
    protected $episodeManager;

    /** @var  \Anime\Model\Category\CategoryManager */
    protected $categoryManager;

    /** @var  \Anime\Model\Species\SpeciesManager */
    protected $speciesManager;

    /** @var  \Anime\Model\Topics\TopicsManager */
    protected $topicsManager;

    /** @var  \Anime\Model\Type\TypeManager */
    protected $typeManager;

    /** @var  \Anime\Model\Link\LinkManager */
    protected $linkManager;

    public function init()
    {
        $this->episodeManager = new EpisodeManager($this->getDatabase(), $this->getCache());
        $this->categoryManager = new CategoryManager($this->getDatabase(), $this->getCache());
        $this->speciesManager = new SpeciesManager($this->getDatabase());
        $this->topicsManager = new TopicsManager($this->getDatabase());
        $this->typeManager = new TypeManager($this->getDatabase());
        $this->linkManager = new LinkManager($this->getDatabase());
    }

    public function categoryAction($cat)
    {
        //check if category exist
        if(false === $data = $this->categoryManager->findOneBy(array('alias' => $cat)))
        {
            throw $this->createNotFoundException(sprintf('Kategoria "%s" nie istnieje.', $cat));
        }

        $user = $this->getUser();

        $ratingTitle = array(
            1 => 'Nieporozumienie',
            2 => 'Bardzo złe',
            3 => 'Słabe',
            4 => 'Ujdzie',
            5 => 'Średnie',
            6 => 'Niezłe',
            7 => 'Dobre',
            8 => 'Bardzo dobre',
            9 => 'Rewelacyjne',
            10 => 'Arcydzieło',
        );

        $category = $this->categoryManager->create($data);

        //update views for category
        $this->categoryManager->updateOf($category);

        //species, topics and types
        $species = array();
        foreach($this->speciesManager->getSpeciesForCategory($category->getId()) as $row)
        {
            $species[] = $row['name'];
        }
        $species = implode(', ', $species);

        $topics = array();
        foreach($this->topicsManager->getTopicsForCategory($category->getId()) as $row)
        {
            $topics[] = $row['name'];
        }
        $topics = implode(', ', $topics);

        $types = array();
        foreach($this->typeManager->getTypeForCategory($category->getId()) as $row)
        {
            $types[] = $row['name'];
        }
        $types = implode(', ', $types);

        //episodes
        $episodes = $this->episodeManager->getEpisodesForCategory($category->getId());

        //user rating, favorite and watching
        $userRating = round($category->getRatingAvg());
        $userFavorite = null;
        $userWatching = null;

        if($user->isUser())
        {
            //rating
            $ratingManager = new RatingManager($this->getDatabase());
            $userRating = $ratingManager->findOneBy(array(
                'user_id' => $user->getId(),
                'category_id' => $category->getId(),
            ));
            $userRating = empty($userRating['value']) ? 0 : $userRating['value'];

            //favorite
            $favoriteManager = new FavoriteManager($this->getDatabase());
            $userFavorite = $favoriteManager->findOneBy(array(
                'category_id' => $category->getId(),
                'user_id' => $user->getId(),
            ));

            //watching
            /** @var \Anime\Model\Watch\WatchBag $userWatching */
            $userWatching = $this->getSession()->get('watched') ?: new WatchBag(array());
            $userWatching = $userWatching->get($category->getId());
        }

        if($category->getSeries())
        {
            $similar = $this->categoryManager->getSeries($category->getSeries(), ($episodes->rowCount() > 5 ? 50 : 10));
            $similarTitle = 'Pokrewne';
        } else
        {
            $similar = $this->categoryManager->getSimilar($category->getId(), ($episodes->rowCount() > 5 ? 15 : 10));
            $similarTitle = 'Podobne';
        }

        return $this->render('Episodes/category', array(
            'category' => $category,
            'species' => $species,
            'topics' => $topics,
            'types' => $types,
            'episodes' => $episodes,
            'rating_title' => $ratingTitle,
            'user_rating' => $userRating,
            'user_favorite' => $userFavorite,
            'user_watching' => $userWatching,
            'similar' => $similar,
            'similar_title' => $similarTitle,
        ));
    }

    public function watchedAction($cat, $type)
    {
        $user = $this->getUser();

        if(false == $user->isUser())
        {
            $this->getSession()->getFlashBag()->set('msg', array(
                'danger' => 'Aby móc zmienić status, musisz być zalogowany.',
            ));

            return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
        }

        //check if category exist
        if(false === $data = $this->categoryManager->findOneBy(array('alias' => $cat)))
        {
            throw $this->createNotFoundException(sprintf('Kategoria "%s" nie istnieje.', $cat));
        }

        $category = $this->categoryManager->create($data);

        if((!$category->getStatus() && 2 == $type) || (2 == $category->getStatus() &&  '3' !== $type))
        {
            if(!$category->getStatus())
            {
                $msg = 'Nie można ustawić statusu <strong>obejrzałem</strong> dla emitowanych anime.';
            } else
            {
                $msg = 'Dla <strong>nadchodzących</strong> anime można ustawić tylko status <strong>planuje</strong>.';
            }

            $this->getSession()->getFlashBag()->set('msg', array('danger' => $msg));

            return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
        }

        $watchedManager = new WatchManager($this->getDatabase());

        /** @var \Anime\Model\Watch\WatchBag $userWatching */
        $watchBag = $this->getSession()->get('watched');

        if($watchBag->has($category->getId(), $type))
        {
            return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
        }

        $criteria = array(
            'user_id' => $user->getId(),
            'category_id' => $category->getId()
        );

        if($watchedManager->exists($criteria + array('type' => $type)))
        {
            return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
        }

        $types = array(
            1 => array(
                'title' => 'Oglądam',
                'column' => CategoryManager::UPDATE_WATCHING,
            ),
            2 => array(
                'title' => 'Obejrzałem',
                'column' => CategoryManager::UPDATE_WATCHED,
            ),
            3 => array(
                'title' => 'Planuje obejrzeć',
                'column' => CategoryManager::UPDATE_PLANS,
            ),
            4 => array(
                'title' => 'Wstrzymałem się',
                'column' => CategoryManager::UPDATE_STOPPED,
            ),
            5 => array(
                'title' => 'Porzuciłem',
                'column' => CategoryManager::UPDATE_ABANDONED,
            ),
        );

        $this->getDatabase()->beginTransaction();

        if(false !== $data = $watchedManager->findOneBy($criteria))
        {
            $watch = $watchedManager->create($data);

            $msg = 'Status został zmieniony z <strong>'.$types[$watch->getType()]['title'].'</strong> na <strong>'.$types[$type]['title'].'</strong>.';

            //move to trigger in mysql
            //$this->categoryManager->updateOf($category, $types[$watch->getType()]['column'], false);
            //$user->setCustomField($types[$watch->getType()]['column'], $user->getCustomField($types[$watch->getType()]['column']) -1);

            $watch->setType($type);

            //move to trigger in mysql
            //$this->categoryManager->updateOf($category, $types[$watch->getType()]['column'], true);
            //$user->setCustomField($types[$watch->getType()]['column'], $user->getCustomField($types[$watch->getType()]['column']) +1);
        } else
        {
            $watch = $watchedManager->create($criteria + array('type' => $type));
            $msg = 'Status <strong>'.$types[$type]['title'].'</strong> został ustawiony.';

            //move to trigger in mysql
            //$this->categoryManager->updateOf($category, $types[$watch->getType()]['column'], true);
            //$user->setCustomField($types[$watch->getType()]['column'], $user->getCustomField($types[$watch->getType()]['column']) +1);
        }

        $watchedManager->update($watch);

        //move to trigger in mysql
        //$this->get('user_manager')->updateUser($user);

        $this->getDatabase()->commit();

        $watchBag->set($category->getId(), $type);
        $this->getSession()->getFlashBag()->set('msg', $msg);

        return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
    }

    public function ratingAction($cat, $value)
    {
        $user = $this->getUser();

        if(false == $user->isUser())
        {
            $this->getSession()->getFlashBag()->set('msg', array(
                'danger' => 'Aby móc ocenić anime musisz być zalogowany.',
            ));

            return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
        }

        //check if category exist
        if(false === $data = $this->categoryManager->findOneBy(array('alias' => $cat)))
        {
            throw $this->createNotFoundException(sprintf('Kategoria "%s" nie istnieje.', $cat));
        }

        $category = $this->categoryManager->create($data);

        if(2 == $category->getStatus())
        {
            $this->getSession()->getFlashBag()->set('msg', array(
                'danger' => 'Nie można ocenić <strong>nadchodzącego</strong> anime.',
            ));

            return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
        }

        $ratingManager = new RatingManager($this->getDatabase());

        $criteria = array(
            'user_id' => $user->getId(),
            'category_id' => $category->getId(),
        );

        $this->getDatabase()->beginTransaction();

        if('delete' == $value)
        {
            $ratingManager->deleteWhere($criteria);
            $ratingManager->updateCategoryAvg($category->getId());

            //moved to trigger in mysql
            //$this->categoryManager->updateOf($category, CategoryManager::UPDATE_RATING, false);

            $msg = 'Twoja ocena dla anime <strong>'.$category->getName().'</strong> została usunięta.';
        } elseif($value >= 1 || $value <= 10)
        {
			if(false !== $data = $ratingManager->findOneBy($criteria))
			{
				$data = $ratingManager->create($data);
				$msg = 'Twoja ocena została zmieniona z <strong>'.$data->getValue().'</strong> na <strong>'.$value.'</strong>.';
				$data->setValue($value);
			} else
			{
				$data = $ratingManager->create($criteria);
				$data->setValue($value);

				//moved to trigger in mysql
				//$this->categoryManager->updateOf($category, CategoryManager::UPDATE_RATING);

				$msg = 'Twoja ocena <strong>'.$value.'</strong> została dodana.';
			}

			$ratingManager->update($data);
			$ratingManager->updateCategoryAvg($category->getId());
        }

        //update count of total rating for user
        //moved to trigger in mysql
        //$user->setCustomField('rated', $ratingManager->countForUser($user->getId()));
        //$this->get('user_manager')->updateUser($user);

        $this->getSession()->getFlashBag()->set('msg', $msg);

        $this->getDatabase()->commit();

        return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
    }

    public function favoriteAction($cat)
    {
        $user = $this->getUser();

        if(false == $user->isUser())
        {
            $this->getSession()->getFlashBag()->set('msg', array(
                'danger' => 'Aby móc dodać anime do ulubionych musisz być zalogowany.',
            ));

            return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
        }

        //check if category exist
        if(false === $data = $this->categoryManager->findOneBy(array('alias' => $cat)))
        {
            throw $this->createNotFoundException(sprintf('Kategoria "%s" nie istnieje.', $cat));
        }

        $category = $this->categoryManager->create($data);
        $favoriteManager = new FavoriteManager($this->getDatabase());

        $criteria = array(
            'user_id' => $user->getId(),
            'category_id' => $category->getId(),
        );

        $this->getDatabase()->beginTransaction();

        //check if favorite exist
        if(false !== $data = $favoriteManager->findOneBy($criteria))
        {
            $favoriteManager->delete($favoriteManager->create($data));

            $increase = false;
        } else
        {
            $favoriteManager->update($favoriteManager->create($criteria));

            $increase = true;
        }

        $this->getSession()->getFlashBag()->set('msg', 'Anime <strong>'.htmlspecialchars($category->getName()).'</strong> zostało '.($increase ? 'dodane do' : 'usunięte z').' ulubionych.');

        //update count favorites for user
        //moved to mysql trigger
        //$user->setCustomField('favorites', $favoriteManager->countByUser($user->getId()));
        //$this->get('user_manager')->updateUser($user);
        //$this->categoryManager->updateOf($category, CategoryManager::UPDATE_FAVORITE, $increase);

        $this->getDatabase()->commit();

        return $this->redirect($this->generateUrl('episodes_cat', array('cat' => $cat)));
    }

    public function showAction($cat, $id)
    {
        //check if category exist
        if(false === $data = $this->categoryManager->findOneBy(array('alias' => $cat)))
        {
            throw $this->createNotFoundException(sprintf('Kategoria "%s" nie istnieje.', $cat));
        }

        $category = $this->categoryManager->create($data);

        //update views for category
        $category->setViews($category->getViews() + 1);
        $this->categoryManager->updateOf($category, CategoryManager::UPDATE_VIEWS);

        //check if episode exist
        if(false === $data = $this->episodeManager->findOneBy(array('category_id' => $category->getId(), 'number' => $id)))
        {
            throw $this->createNotFoundException(sprintf('Odcinek "%s" w kategorii "%s" nie istnieje.', $id, $cat));
        }

        $episode = $this->episodeManager->create($data);

        //find links for episode
        $links = $this->linkManager->findLinksBy(array('episode_id' => $episode->getId()), 'FIELD(lang_id, '.Link::LANG_ID_PL.', '.Link::LANG_ID_EN.', '.Link::LANG_ID_JP.'), s.name ASC');

        $neighbours = $this->episodeManager->getNeighbours($category->getId(), $episode->getNumber());

        if($category->getSeries())
        {
            $similar = $this->categoryManager->getSeries($category->getSeries(), 10);
            $similarTitle = 'Pokrewne';
        } else
        {
            $similar = $this->categoryManager->getSimilar($category->getId(), 10);
            $similarTitle = 'Podobne';
        }

        return $this->render('Episodes/show', array(
            'category' => $category,
            'episode' => $episode,
            'links' => $links,
            'position' => 1,
            'video_prefix' => $this->get('video_prefix'),
            'video_salt' => $this->get('video_salt'),
            'neighbours' => $neighbours,
            'similar' => $similar,
            'similar_title' => $similarTitle,
        ));
    }

    public function showLinkAction(Request $request)
    {
        //check referer
        if(!preg_match('#'.$request->getHost().'#', $request->headers->get('referer')))
        {
            throw $this->createNotFoundException(sprintf('Niepoprawny referer: "%s"', $request->getHost()));
        }

        if(!$this->getSession()->get('secure_image', false))
        {
            throw $this->createNotFoundException('Obraz zabezpieczający nie został wyświetlony poprawnie.');
        }

        $data = explode(':', $request->request->get('data'));

        if(count($data) !== 2)
        {
            throw $this->createNotFoundException(sprintf('Niepoprawne przesłane dane: "%s"', implode(';', $data)));
        }

        list($linkId, $hash) = $data;

        if($hash !== md5($linkId.$this->get('video_salt')))
        {
            throw $this->createNotFoundException('Podany $hash jest różny od $linkId');
        }

        if(false == $data = $this->linkManager->findOneBy(array('id' => $linkId)))
        {
            return $this->createNotFoundException(sprintf('Link o podanym ID: "%s" nie istnieje.', $linkId));
        }

        $link = $this->linkManager->create($data);
        $serverManager = new ServerManager($this->getDatabase());
        $server = $serverManager->create($serverManager->findOneBy(array('id' => $data['server_id'])));

        return new Response($server->getHtml($link->getCode()));
    }

    public function addNewAction(Request $request)
    {
        $errorMsg = array();

        $submittedEpisodeManager = new SubmittedEpisodeManager($this->getDatabase());
        $episode = $submittedEpisodeManager->create();

        $faqManager = new FaqManager($this->getDatabase());

        if($request->isMethod('post'))
        {
            try
            {
                $episode = $submittedEpisodeManager->create($request->request->get('episode', array()));
            } catch(\Exception $e)
            {
                throw $this->createNotFoundException($e->getMessage());
            }

            $validTitle = new StringLength(array('min' => 6, 'max' => 200));
            if(!$validTitle->isValid($episode->getTitle()))
            {
                $errorMsg[] = 'Tytuł jest zbyt krótki, lub zbyt długi. Minimum 6 znaków, lub maksimum 200.';
            }

            if(!$episode->getLinks())
            {
                $errorMsg[] = 'Nie podałeś żadnych linków!';
            }

            if(strcmp($request->request->get('code'), $this->getSession()->get('captcha')))
            {
                $errorMsg[] = 'Przepisany kod jest niepoprawny.';
            } else
            {
                $this->getSession()->remove('captcha');
            }

            if(empty($errorMsg))
            {
                $episode->setIp($request->server->get('REMOTE_ADDR'));

                $submittedEpisodeManager->update($episode);

                $this->getSession()->getFlashBag()->set('msg', 'Twoja wiadomość została przesłana.');

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render('Episodes/addNew', array(
            'episode' => $episode,
            'error_msg' => $errorMsg,
            'sidebar' => $faqManager->findAll(),
        ));
    }
} 