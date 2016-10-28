<?php


namespace Anime\Controller;


use Anime\Model\Faq\FaqManager;
use Anime\Model\Pages\PagesManager;
use Sequence\Controller;

class PagesController extends Controller
{
    public function showAction($alias)
    {
        $pagesManager = new PagesManager($this->getDatabase());
        $faqManager = new FaqManager($this->getDatabase());

        if(false == $page = $pagesManager->findOneBy(array('alias' => $alias)))
        {
            throw $this->createNotFoundException(sprintf('Strona o podanym aliasie: "%s" nie istnieje.', $alias));
        }

        return $this->render('Pages/show', array(
            'page' => $pagesManager->create($page),
            'sidebar' => $faqManager->findAll(),
        ));
    }
} 