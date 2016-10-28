<?php


namespace Admin\Controller;


use Anime\Model\Comment\CommentManager;
use Anime\Model\Link\LinkManager;
use Anime\Model\Report\Report;
use Anime\Model\Report\ReportManager;
use Anime\Model\Server\ServerManager;
use Sequence\Controller;
use Sequence\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;

class ReportsController extends Controller
{
    /** @var  \Anime\Model\Report\ReportManager */
    protected $reports;

    public function init()
    {
        $this->reports = new ReportManager($this->getDatabase());
    }

    public function indexAction(Request $request)
    {
        $list = $this->reports->findListBy(array());

        $pagination =  new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('reports_index'))->
            setUrl($this->generateUrl('reports_index', array('page' => '_PAGE_')))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Reports/index', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
        ));
    }

    public function viewAction($reportID)
    {
        if(!$report = $this->reports->find($reportID))
        {
            throw $this->createNotFoundException();
        }

        $info = array();
        $report = $this->reports->create($report);

        if(Report::TYPE_LINK == $report->getType())
        {
            $links = new LinkManager($this->getDatabase());

            if(false == $info = $links->findCategory($report->getLinkId()))
            {
                $this->reports->delete($report);

                throw $this->createNotFoundException(sprintf('Category "%s" does not exists.', $report->getLinkId()));
            }

            $servers = new ServerManager($this->getDatabase());

            if(false == $list = $servers->findOneBy(array('id' => $info['server_id'])))
            {
                $this->reports->delete($report);

                throw $this->createNotFoundException(sprintf('Server "%s" does not exists.', $info['server_id']));
            }

            $server = $servers->create($list);

            $info['server'] = $server->getName();
        } elseif(Report::TYPE_COMMENT == $report->getType())
        {
            $comments = new CommentManager($this->getDatabase());

            if(false == $comment = $comments->findOneBy(array('id' => $report->getLinkId())))
            {
                $this->reports->delete($report);

                throw $this->createNotFoundException(sprintf('Comment "%s" does not exists.', $report->getLinkId()));
            }

            $comment = $comments->create($comment);

            $info = array(
                'user' => $comment->getUserId(),
                'comment' => $comment,
            );
        }

        return $this->render('Reports/view', array(
            'report' => $report,
            'info' => $info,
        ));
    }

    public function replyAction($reportID, Request $request)
    {
        if(!$request->request->has('message') || !$report = $this->reports->find($reportID))
        {
            throw $this->createNotFoundException();
        }

        $report = $this->reports->create($report);

        $message = $request->request->get('message');
        $message .= "\n\n\n> ".str_replace("\n", '> ', $message);

        /** @var \Sequence\Mail\Mailer $mail */
        $mail = $this->get('mailer');
        $mail->addAddress($report->getMail());
        $mail->Subject = 'RE: '.$report->getSubject();
        $mail->Body = $message;
        $mail->send();

        $this->getSession()->getFlashBag()->set('msg', sprintf('Odpowiedź została przesłana na adres <strong>%s</strong>', $report->getMail()));

        return $this->redirect($request->headers->get('referer', $this->generateUrl('reports_index')));
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

            foreach($toDelete as $id)
            {
                $this->reports->deleteWhere(array('id' => $id));
            }

            $session->remove('to_delete');
            $session->getFlashBag()->add('msg', sprintf('Raporty (<strong>%s</strong>) zostały usunięte.', count($toDelete)));
        } else
        {
            return $this->render('confirm', array(
                'msg' => sprintf('Czy chcesz usunąć wszystkie zaznaczone raporty (<strong>%s</strong>)?', count($session->get('to_delete'))),
                'action' => $this->generateUrl('reports_update'),
            ));
        }

        return $this->redirect($this->generateUrl('reports_index'));
    }

    public function deleteAction($reportID, Request $request)
    {
        if(!$report = $this->reports->find($reportID))
        {
            throw $this->createNotFoundException();
        }

        $report = $this->reports->create($report);

        if($request->isMethod('post'))
        {
            if($reportID !== $request->request->get('id'))
            {
                throw $this->createNotFoundException();
            }

            $this->reports->delete($report);

            $this->getSession()->getFlashBag()->add('msg', sprintf('Raport <strong>%s</strong> został usunięty.', $report->getId()));

            return $this->redirect($this->generateUrl('reports_index'));
        }

        return $this->render('Reports/delete', array(
            'report' => $report,
        ));
    }

    public function searchAction(Request $request)
    {
        if($request->isMethod('post'))
        {
            return $this->redirect($this->generateUrl('reports_search', array(
                'subject' => ($request->request->get('subject') === '' ? null : $request->request->get('subject')),
                'mail' => ($request->request->get('mail') === '' ? null : $request->request->get('mail')),
                'report_ip' => ($request->request->get('report_ip') === '' ?  null : $request->request->get('report_ip')),
                'type' => ($request->request->get('type') === '' ?  null : $request->request->get('type')),
            )));
        }

        $query = array();

        if($request->query->has('subject'))
        {
            $query['subject LIKE'] = '%'.$request->query->get('subject').'%';
        }

        if($request->query->has('mail'))
        {
            $query['mail LIKE'] = '%'.$request->query->get('mail').'%';
        }

        if($request->query->has('report_ip'))
        {
            $query['report_ip LIKE'] = '%'.$request->query->get('report_ip').'%';
        }

        if($request->query->has('type'))
        {
            $query['type'] = $request->query->get('type');
        }

        if(empty($query))
        {
            return $this->redirect($this->generateUrl('reports_index'));
        }

        $list = $this->reports->findListBy($query);

        $pagination = new Pagination();
        $pagination->
            setBaseUrl($this->generateUrl('reports_search', array(
                'subject' => $request->query->get('subject'),
                'mail' => $request->query->get('mail'),
                'report_ip' => $request->query->get('report_ip'),
                'type' => $request->query->get('type'),
            )))->
            setUrl($this->generateUrl('reports_search', array(
                'page' => '_PAGE_',
                'subject' => $request->query->get('subject'),
                'mail' => $request->query->get('mail'),
                'report_ip' => $request->query->get('report_ip'),
                'type' => $request->query->get('type'),
            )))->
            setPerPage(20)->
            setRange(2)->
            setUrlNeedle('_PAGE_')->
            setTotalCount($total = $list->get()->rowCount())->
            setCurrentPage($request->query->get('page', 1));

        $list = $list->offset($pagination->offset())->limit($pagination->limit())->order('id DESC')->get();

        return $this->render('Reports/search', array(
            'total' => $total,
            'list' => $list,
            'pagination' => $pagination->getHtml('float:right;margin:0', 'pagination-sm'),
            'subject' => $request->query->get('subject'),
            'mail' => $request->query->get('mail'),
            'report_ip' => $request->query->get('report_ip'),
            'type' => $request->query->get('type'),
        ));
    }
} 