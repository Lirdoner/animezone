<?php


namespace Anime\Controller;


use Anime\Model\Category\CategoryManager;
use Anime\Model\Category\RatingEntity;
use Anime\Model\Category\RatingRepository;
use Anime\Model\Faq\FaqManager;
use Anime\Model\Species\SpeciesManager;
use Anime\Model\Topics\TopicsManager;
use Anime\Model\Type\TypeManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class SpeciesController extends Controller
{
    public function indexAction(Request $request)
    {
        $faqManager = new FaqManager($this->getDatabase());
        $typeManager = new TypeManager($this->getDatabase());
        $speciesManager = new SpeciesManager($this->getDatabase());
        $topicsManager = new TopicsManager($this->getDatabase());
        $result = null;
        $condition = array();
        $totalCount = 0;

        if(null !== $type = $request->query->get('type'))
        {
            $condition['types'] = $type;
        }

        if(null !== $species = $request->query->get('species'))
        {
            $condition['species'] = $species;
        }

        if(null !== $topic = $request->query->get('topic'))
        {
            $condition['topics'] = $topic;
        }

        $pagination = new Pagination();

        $repository = new RatingRepository($request);
        $repository->add(new RatingEntity('name'));
        $repository->add(new RatingEntity('fans'));
        $repository->add(new RatingEntity('rating_avg'));
        $repository->add(new RatingEntity('views'));

        if($request->query->has('fans'))
        {
            $repository->setCurrent('fans');
        } elseif($request->query->has('rating_avg'))
        {
            $repository->setCurrent('rating_avg');
        } elseif($request->query->has('views'))
        {
            $repository->setCurrent('views');
        } else
        {
            $repository->setCurrent('name');
        }

        if(!empty($condition))
        {
            $pagination->
                setBaseUrl($this->generateUrl('anime_species', array('type' => $type, 'species' => $species, 'topic' => $topic, $repository->getCurrent()->getColumn() => $repository->getOrder('readable'))))->
                setUrl($this->generateUrl('anime_species', array('type' => $type, 'species' => $species, 'topic' => $topic, 'page' => '_PAGE_', $repository->getCurrent()->getColumn() => $repository->getOrder('readable'))))->
                setPerPage(20)->
                setRange(1)->
                setUrlNeedle('_PAGE_');

            $categoryManager = new CategoryManager($this->getDatabase());

            $result = $categoryManager->getSpeciesBy($condition, $repository->getCurrent()->getColumn().' '.$repository->getOrder('sql'));

            $pagination->setTotalCount($totalCount = $result->get()->rowCount());

            try
            {
                $pagination->setCurrentPage($request->query->get('page', 1));
            } catch(\InvalidArgumentException $e)
            {
                throw $this->createNotFoundException();
            }


            $result = $result->offset($pagination->offset())->limit($pagination->limit())->get();
        }

        return $this->render('Species/index', array(
            'sidebar' => $faqManager->findAll(),
            'types' => $typeManager->findBy(array(), 'name ASC'),
            'current_type' => $type,
            'species' => $speciesManager->findBy(array(), 'name ASC'),
            'current_species' => $species,
            'topics' => $topicsManager->findBy(array(), 'name ASC'),
            'current_topic' => $topic,
            'result' => $result,
            'total_count' => $totalCount,
            'pagination' => $pagination,
            'repository' => $repository,
        ));
    }
} 