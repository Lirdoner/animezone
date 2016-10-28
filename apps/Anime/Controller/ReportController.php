<?php


namespace Anime\Controller;


use Anime\Model\Comment\CommentManager;
use Anime\Model\Link\LinkManager;
use Anime\Model\Report\Report;
use Anime\Model\Report\ReportManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sequence\Controller;

class ReportController extends Controller
{
    /**
     * @var \Anime\Model\Report\ReportManager
     */
    protected $reportManager;

    public function init()
    {
        $this->reportManager = new ReportManager($this->getDatabase());
    }

    public function commentAction(Request $request)
    {
        $commentManager = new CommentManager($this->getDatabase());

        if(false == $comment = $commentManager->findOneBy(array('id' => $request->request->get('id'))))
        {
            throw $this->createNotFoundException(sprintf('Komentarz o podanym ID: "%s" nie istnieje.', $comment));
        }

        $report = $this->reportManager->create();
        $report->setType(Report::TYPE_COMMENT);
        $report->setLinkId($comment['id']);
        $report->setReportIp($request->server->get('REMOTE_ADDR'));

        if($this->reportManager->findOneBy(array('type' => $report->getType(), 'link_id' => $report->getLinkId())))
        {
            $response = 'Komentarz został już wcześniej złgoszony. Prosimy o nie dublowanie zgłoszeń - w żaden sposób to nie przyspiesza naszej reakcji.';
        } else
        {
            $this->reportManager->update($report);
            $response = 'Zgłoszenie zostało przesłane.';
        }

        return new Response($response);
    }

    public function linkAction(Request $request)
    {
        $linkManager = new LinkManager($this->getDatabase());

        if(false == $link = $linkManager->findOneBy(array('id' => $request->request->get('id'))))
        {
            throw $this->createNotFoundException(sprintf('Link o podanym ID: "%s" nie istnieje.', $link));
        }

        $report = $this->reportManager->create();
        $report->setType(Report::TYPE_LINK);
        $report->setLinkId($link['id']);
        $report->setReportIp($request->server->get('REMOTE_ADDR'));

        if($this->reportManager->findOneBy(array('type' => $report->getType(), 'link_id' => $report->getLinkId())))
        {
            $response = 'Problem z linkiem został już zgłoszony, prosimy nie zgłaszać kilkakrotnie tych samych linków - w żaden sposób to nie przyspiesza ich naprawy.';
        } else
        {
            $this->reportManager->update($report);
            $response = 'Zgłoszenie zostało przesłane.';
        }

        return new Response($response);
    }
} 