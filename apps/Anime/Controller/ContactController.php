<?php


namespace Anime\Controller;


use Anime\Model\Faq\FaqManager;
use Anime\Model\Report\Report;
use Anime\Model\Report\ReportManager;
use Sequence\Controller;
use Sequence\Validator\StringLength;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    public function indexAction(Request $request)
    {
        $errorMsg = array();

        $reportManager = new ReportManager($this->getDatabase());
        $report = $reportManager->create();

        $faqManager = new FaqManager($this->getDatabase());

        if(!$report->getSubject())
        {
            $report->setSubject($request->query->get('title'));
        }

        if($request->isMethod('post'))
        {
            try
            {
                $report = $reportManager->create($request->request->get('report', array()));
            } catch(\Exception $e)
            {
                throw $this->createNotFoundException($e->getMessage());
            }

            $validSubject = new StringLength(array('min' => 6, 'max' => 100));
            if(!$validSubject->isValid($report->getSubject()))
            {
                $errorMsg[] = 'Temat jest zbyt krótki, lub zbyt długi. Minimum 6 znaków, lub maksimum 100.';
            }

            if($this->getUser()->isUser())
            {
                $report->setMail($this->getUser()->getEmail());
            } else
            {
                if(!filter_var($report->getMail(), FILTER_VALIDATE_EMAIL))
                {
                    $errorMsg[] = 'Adres E-mail jest niepoprawny.';
                }
            }

            if(!$report->getContent())
            {
                $errorMsg[] = 'Treść wiadomości jest pusta.';
            }

            if(strcmp($request->request->get('code'), $this->getSession()->get('captcha')))
            {
                $errorMsg[] = 'Przepisany kod z obrazka jest niepoprawny.';
            } else
            {
                $this->getSession()->remove('captcha');
            }

            if(empty($errorMsg))
            {
                $report->setType(Report::TYPE_CONTACT);
                $report->setLinkId(0);
                $report->setReportIp($request->server->get('REMOTE_ADDR'));

                $reportManager->update($report);

                $this->getSession()->getFlashBag()->set('msg', 'Twoja wiadomość została przesłana.');

                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->render('Contact/index', array(
            'report' => $report,
            'error_msg' => $errorMsg,
            'sidebar' => $faqManager->findAll(),
        ));
    }
} 