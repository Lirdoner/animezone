<?php


namespace Admin\Controller;


use Anime\Model\Category\CategoryManager;
use Anime\Model\Comment\CommentManager;
use Anime\Model\Series\SeriesManager;
use Anime\Model\Species\SpeciesForCategoryManager;
use Anime\Model\Species\SpeciesManager;
use Anime\Model\Topics\TopicsForCategoryManager;
use Anime\Model\Topics\TopicsManager;
use Anime\Model\Type\TypeForCategoryManager;
use Anime\Model\Type\TypeManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoriesController extends Controller
{
    /** @var  \Anime\Model\Category\CategoryManager */
    protected $categoryManager;

    /** @var  \Anime\Model\Series\SeriesManager */
    protected $series;

    /** @var  \Anime\Model\Species\SpeciesManager */
    protected $species;

    /** @var  \Anime\Model\Species\SpeciesForCategoryManager */
    protected $speciesForCategory;

    /** @var  \Anime\Model\Topics\TopicsManager */
    protected $topics;

    /** @var  \Anime\Model\Topics\TopicsForCategoryManager */
    protected $topicsForCategory;

    /** @var  \Anime\Model\Type\TypeManager */
    protected $types;

    /** @var  \Anime\Model\Type\TypeForCategoryManager */
    protected $typeForCategory;

    public function init()
    {
        /** @var \Sequence\Cache\Cache $cache */
        $cache = $this->get('front_cache');

        $this->categoryManager = new CategoryManager($this->getDatabase(), $cache);
        $this->series = new SeriesManager($this->getDatabase(), $cache);
        $this->species = new SpeciesManager($this->getDatabase());
        $this->speciesForCategory = new SpeciesForCategoryManager($this->getDatabase());
        $this->topics = new TopicsManager($this->getDatabase());
        $this->topicsForCategory = new TopicsForCategoryManager($this->getDatabase());
        $this->types = new TypeManager($this->getDatabase());
        $this->typeForCategory = new TypeForCategoryManager($this->getDatabase());

        $letters = range('A', 'Z');
        $letters[] = 0;
        sort($letters, SORT_STRING);

        $cat = $this->categoryManager->create();

        $this->get('templating')->addGlobal('_letters', $letters);
        $this->get('templating')->addGlobal('_season', $cat->getSeasonType());
        $this->get('templating')->addGlobal('_status', $cat->getStatusType());
        $this->get('templating')->addGlobal('_release', $cat->getReleaseType());
    }

    public function indexAction(Request $request)
    {
        $list = $this->categoryManager->findListBy(array());

        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('categories_index'))->
            setUrl($this->generateUrl('categories_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Categories/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm')
        ));
    }

    public function createAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            $category = $this->categoryManager->create($request->request->get('category'));

            //check if image is send as file
            if($category->getImage() == 'file')
            {
                if($file = $request->files->get('image', false))
                {
                    $file->move($this->get('config')->anime->get('category_images'), $file->getClientOriginalName());
                    $category->setImage($file->getClientOriginalName());
                } else
                {
                    $this->getSession()->getFlashBag()->add('msg', 'Wystąpił błąd podczas odczytu przesłanego obrazu. Spróbuj ponownie.');

                    return $this->redirect($this->generateUrl('categories_create'));
                }
            }

            //save category
            $this->categoryManager->update($category);

            $category->setId($this->getDatabase()->lastInsertId());

            //create connections for species
            foreach($request->request->get('species', array()) as $id => $name)
            {
                $this->speciesForCategory->update($this->speciesForCategory->create(array(
                    'category_id' => $category->getId(),
                    'species_id' => $id,
                )));
            }

            //create connections for topics
            foreach($request->request->get('topics', array()) as $id => $name)
            {
                $this->topicsForCategory->update($this->topicsForCategory->create(array(
                    'category_id' => $category->getId(),
                    'topics_id' => $id,
                )));
            }

            //create connections for types
            foreach($request->request->get('types', array()) as $id => $name)
            {
                $this->typeForCategory->update($this->typeForCategory->create(array(
                    'category_id' => $category->getId(),
                    'type_id' => $id,
                )));
            }

            $this->getSession()->getFlashBag()->add('msg', sprintf('Kategoria <strong>%s</strong> została utworzona.',  $category->getName()));

            $this->categoryManager->clearCache();

            return $this->redirect($this->generateUrl('categories_index'));
        }

        return $this->render('Categories/create', array(
            'series' => $this->series->findAll('name'),
            'species' => $this->species->findAll('name'),
            'topics' => $this->topics->findAll('name'),
            'types' => $this->types->findAll('name'),
        ));
    }

    public function editAction($catID, Request $request)
    {
        if(!$category = $this->categoryManager->findOneBy(array('id' => $catID)))
        {
            throw $this->createNotFoundException();
        }

        $category = $this->categoryManager->create($category);

        $species = array();
        foreach($this->speciesForCategory->findBy(array('category_id' => $category->getId())) as $row)
        {
            $species[$row['species_id']] = $row['id'];
        }

        $topics = array();
        foreach($this->topicsForCategory->findBy(array('category_id' => $category->getId())) as $row)
        {
            $topics[$row['topics_id']] = $row['id'];
        }

        $types = array();
        foreach($this->typeForCategory->findBy(array('category_id' => $category->getId())) as $row)
        {
            $types[$row['type_id']] = $row['id'];
        }


        if($request->isMethod('post'))
        {
            $newCategory = $this->categoryManager->create(array_merge($category->toArray(), $request->request->get('category')));

            //check if image is send as file
            if($newCategory->getImage() == 'file')
            {
                if($file = $request->files->get('image', false))
                {
                    $file->move($this->get('config')->anime->get('category_images'), $file->getClientOriginalName());
                    $newCategory->setImage($file->getClientOriginalName());
                } else
                {
                    $this->getSession()->getFlashBag()->add('msg', 'Wystąpił błąd podczas odczytu przesłanego obrazu. Spróbuj ponownie.');

                    return $this->redirect($this->generateUrl('categories_create'));
                }
            }

            //update category
            $this->categoryManager->update($newCategory);

            //update connections for species
            foreach(array_diff_key($species, $request->request->get('species')) as $i => $id)
            {
                $this->speciesForCategory->deleteWhere(array('id' => $id));
            }

            foreach($request->request->get('species') as $id => $v)
            {
                if(empty($v))
                {
                    $this->speciesForCategory->update($this->speciesForCategory->create(array(
                        'category_id' => $newCategory->getId(),
                        'species_id' => $id,
                    )));
                }
            }

            //update connections for topics
            foreach(array_diff_key($topics, $request->request->get('topics')) as $i => $id)
            {
                $this->topicsForCategory->deleteWhere(array('id' => $id));
            }

            foreach($request->request->get('topics') as $id => $v)
            {
                if(empty($v))
                {
                    $this->topicsForCategory->update($this->topicsForCategory->create(array(
                        'category_id' => $newCategory->getId(),
                        'topics_id' => $id,
                    )));
                }
            }

            //update connections for types
            foreach(array_diff_key($types, $request->request->get('types')) as $i => $id)
            {
                $this->typeForCategory->deleteWhere(array('id' => $id));
            }

            foreach($request->request->get('types') as $id => $v)
            {
                if(empty($v))
                {
                    $this->typeForCategory->update($this->typeForCategory->create(array(
                        'category_id' => $newCategory->getId(),
                        'type_id' => $id,
                    )));
                }
            }

            $this->getSession()->getFlashBag()->add('msg', sprintf('Kategoria <a href="%s"><strong>%s</strong></a> została zaktualizowana.',
                $this->generateUrl('categories_edit', array('catID' => $newCategory->getId())), $newCategory->getName()));

            $this->categoryManager->clearCache();

            return $this->redirect($this->generateUrl('categories_index'));
        }

        return $this->render('Categories/edit', array(
            'category' => $category,
            'series' => $this->series->findAll('name'),
            'species' => $this->species->findAll('name'),
            '_species' => $species,
            'topics' => $this->topics->findAll('name'),
            '_topics' => $topics,
            'types' => $this->types->findAll('name'),
            '_types' => $types,
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

            $comments = new CommentManager($this->getDatabase());

            foreach($toDelete as $id)
            {
                $this->categoryManager->deleteWhere(array('id' => $id));

                //delete comments to category and to episodes
                $comments->deleteWhere(array('to' => $id));
            }

            $this->categoryManager->clearCache();

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Kategorie zostały usunięte (<strong>%s</strong>).', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone katgorie (<strong>%s</strong>)?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('categories_update'),
            ));
        }

        return $this->redirect($this->generateUrl('categories_index'));
    }

    public function deleteAction($catID, Request $request)
    {
        if(!$category = $this->categoryManager->findOneBy(array('id' => $catID)))
        {
            throw $this->createNotFoundException();
        }

        $category = $this->categoryManager->create($category);

        if($request->isMethod('post'))
        {
            if($catID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->categoryManager->delete($category);
            $this->categoryManager->clearCache();

            //delete comments to category and to episodes
            $comments =  new CommentManager($this->getDatabase());
            $comments->deleteWhere(array('to' => $category->getId()));

            $this->getSession()->getFlashBag()->add('msg', sprintf('Kategoria <strong>%s</strong> i powiązania zostały usunięte.', $category->getName()));

            return $this->redirect($this->generateUrl('categories_index'));
        }

        return $this->render('Categories/delete', array(
            'category' => $category,
        ));
    }

    public function searchAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            return $this->redirect($this->generateUrl('categories_search', array(
                'name' => $request->request->get('name') === '' ? null : $request->request->get('name'),
                'letter' => $request->request->get('letter') === '' ? null : $request->request->get('letter'),
                'year' => $request->request->get('year') === '' ? null : $request->request->get('year'),
                'status' => $request->request->get('status') === '' ?  null : $request->request->get('status'),
            )));
        }

        $query = array();

        if($request->query->has('name'))
        {
            $query['name LIKE'] = $request->query->get('name').'%';
        }

        if($request->query->has('letter'))
        {
            $query['letter'] = strtoupper($request->query->get('letter'));
        }

        if($request->query->has('year'))
        {
            $query['year'] = $request->query->get('year');
        }

        if($request->query->has('status'))
        {
            $query['status'] = $request->query->get('status');
        }

        if(empty($query))
        {
            return $this->redirect($this->generateUrl('categories_index'));
        }

        $list = $this->categoryManager->search($query);

        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('categories_search', array(
                'name' => $request->query->get('name'),
                'letter' => $request->query->get('letter'),
                'year' => $request->query->get('year'),
                'status' => $request->query->get('status'),
            )))->
            setUrl($this->generateUrl('categories_search', array(
                'page' => '_PAGE_',
                'name' => $request->query->get('name'),
                'letter' => $request->query->get('letter'),
                'year' => $request->query->get('year'),
                'status' => $request->query->get('status'),
            )))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Categories/search', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
            'name' => $request->query->get('name'),
            'letter' => $request->query->get('letter'),
            'year' => $request->query->get('year'),
            'status' => $request->query->get('status'),
        ));
    }

    public function aliasAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->request->has('query'))
        {
            $category = $this->categoryManager->findOneBy(array('alias' => $request->request->get('query')));

            $response = array();

            if(false !== $category)
            {
                $response['id'] = $category['id'];
                $response['name'] = $category['name'];
            }

            return new JsonResponse($response);
        }

        throw $this->createNotFoundException();
    }

    public function imagesAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->request->has('query'))
        {
            $finder = new Finder();

            $iterator = $finder->
                files()->
                name('/'.$request->request->get('query').'/i')->
                depth(0)->
                in($this->get('config')->anime->get('category_images'));

            $fileList = array();

            foreach($iterator as $file)
            {
                $fileList[] = array(
                    'name' => $file->getFilename(),
                );
            }

            return new JsonResponse($fileList);
        }

        throw $this->createNotFoundException();
    }

    public function listAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->request->has('query'))
        {
            $list = $this->categoryManager->findBy(array('name LIKE' => '%'.$request->request->get('query').'%'));

            $response = array();

            foreach($list as $row)
            {
                $response[] = array(
                    'id' => $row['id'],
                    'name' => $row['name']
                );
            }

            return new JsonResponse($response);
        }

        throw $this->createNotFoundException();
    }

    public function statsAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            return new JsonResponse($this->categoryManager->getStats());
        }

        throw $this->createNotFoundException();
    }
}