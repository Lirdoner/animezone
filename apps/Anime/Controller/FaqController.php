<?php


namespace Anime\Controller;


use Anime\Model\Faq\FaqManager;
use Anime\Model\News\NewsManager;
use Sequence\Controller;

class FaqController extends Controller
{
    public function indexAction()
    {
        $faqManager = new FaqManager($this->getDatabase());
        $newsManager = new NewsManager($this->getDatabase(), $this->getCache());

        return $this->render('Faq/index', array(
            'list' => $faqManager->findBy(array(), 'id ASC'),
            'sidebar' => $newsManager->getSidebar(),
        ));
    }
} 