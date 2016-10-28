<?php


namespace Anime\Controller;


use Anime\Model\Category\CategoryManager;
use Anime\Model\Faq\FaqManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function indexAction(Request $request)
    {
        $result = $error = $totalCount = null;
        $query = htmlspecialchars($request->query->get('q', false));

        $faqManager = new FaqManager($this->getDatabase());

        if($request->isMethod('post'))
        {
            if($postQuery = $request->get('query'))
            {
                if(strlen(urlencode($postQuery)) >= 3)
                {
                    return $this->redirect($request->getBaseUrl().'/szukaj?q='.urlencode($postQuery));
                } else
                {
                    $error = 'Wpisana ilość znaków jest zbyt mała. Minimum to 3 znaki.';
                }
            }
        }

        $pagination = new Pagination();

        if(false !== $query)
        {
            $query = urldecode($query);

            if(strlen($query) >= 3)
            {
                $pagination->
                    setBaseUrl($this->generateUrl('search', array('q' => $query)))->
                    setUrl($this->generateUrl('search', array('q' => $query, 'page' => '_PAGE_')))->
                    setPerPage(20)->
                    setRange(3)->
                    setUrlNeedle('_PAGE_');

                $categoryManager = new CategoryManager($this->getDatabase());

                $result = $categoryManager->search(array(
                    'name LIKE' => '%'.$query.'%',
                    'description LIKE' => '%'.$query.'%',
                ));

                $pagination->setTotalCount($totalCount = $result->get()->rowCount());

                try
                {
                    $pagination->setCurrentPage($request->query->get('page', 1));
                } catch(\InvalidArgumentException $e)
                {
                    throw $this->createNotFoundException();
                }

                $result = $result->offset($pagination->offset())->limit($pagination->limit())->get();
            } else
            {
                $error = 'Wpisana ilość znaków jest zbyt mała. Minimum to 3 znaki.';
            }
        }

        return $this->render('Search/index', array(
            'sidebar' => $faqManager->findAll(),
            'query' => $query,
            'result' => $result,
            'error' => $error,
            'total_count' => $totalCount,
            'pagination' => $pagination,
        ));
    }
}