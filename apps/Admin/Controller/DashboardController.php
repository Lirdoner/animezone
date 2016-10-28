<?php


namespace Admin\Controller;


use Anime\Model\Comment\CommentManager;
use Anime\Model\Report\ReportManager;
use Anime\Model\SubmittedEpisode\SubmittedEpisodeManager;
use Dubture\Monolog\Reader\LogReader;
use Sequence\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function indexAction()
    {
        $db = $this->getDatabase();

        $note = $db->select()->from('note')->get()->fetch();

        $submittedManager = new SubmittedEpisodeManager($db);
        $submitted = $submittedManager->findListBy(array(), 'id DESC', 10)->get();

        $reportManager = new ReportManager($db);
        $reports = $reportManager->findListBy(array(), 'id DESC', 10)->get();

        $commentManager = new CommentManager($db);
        $comments = $commentManager->findListBy(array(), 'date DESC', 10);

        return $this->render('Dashboard/index', array(
            'note' => $note,
            'submitted' => $submitted,
            'reports' => $reports,
            'comments' => $comments,
        ));
    }

    public function updateNoteAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->request->has('note'))
        {
            $this->getDatabase()->
                update('note', array(
                    'text' => $request->request->get('note')
                ))->
                get();

            return new Response();
        }

        throw $this->createNotFoundException();
    }

    public function logsAction(Request $request)
    {
        $path = array(
            'frontend' => $this->getConfig()->framework->get('root_dir').'/apps/Anime/logs/error.log',
            'backend' => $this->getConfig()->framework->get('logs_dir').'/error.log',
        );

        if(!is_readable($path[$request->query->get('app', 'backend')]))
        {
            if($request->isXmlHttpRequest())
            {
                return new JsonResponse(array('frontend' => 0, 'backend' => 0));
            }

            $reader = array();
        } else
        {
            $reader = new LogReader($path[$request->query->get('app', 'backend')]);
        }

        if($request->isXmlHttpRequest())
        {
            if(null == $count = $this->getCache()->get('dashboard/logs'))
            {
                $frontend = (new LogReader($path['frontend']))->count();
                $backend = (new LogReader($path['backend']))->count();

                $count = array(
                    'frontend' => ($frontend == 0 ?: $frontend-1),
                    'backend' => ($backend == 0 ?: $backend-1),
                );

                $this->getCache()->set('dashboard/logs', $count, 86400);
            }

            return new JsonResponse($count);
        }

        $logs = array();

        foreach($reader as $i => $v)
        {
            if(!empty($v))
            {
                $logs[] = array(
                    'date' => (isset($v['date']) && $v['date'] instanceof \DateTime ? $v['date']->format('Y-m-d H:i:s') : null),
                    'level' => (isset($v['level']) ? $v['level'] : null),
                    'message' => (isset($v['message']) ? $v['message'] : null),
                    'context' => (isset($v['context']) ? $v['context'] : null),
                );

                if($i == 80)
                {
                    break;
                }
            }
        }

        return $this->render('Dashboard/logs', array(
            'logs' => $logs,
            'frontend' => $request->query->get('app'),
        ));
    }
}