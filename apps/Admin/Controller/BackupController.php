<?php




namespace Admin\Controller;





use Sequence\Controller;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Finder\Finder;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\StreamedResponse;



class BackupController extends Controller

{

    private $backupPath;



    public function init()

    {
        $this->backupPath = $this->getConfig()->framework->root_dir.'/backups';



        if(!is_writable($this->backupPath))

        {

            throw new \RuntimeException(sprintf('Path "%s" is not writable.'));

        }

    }



    public function indexAction()

    {

        $files = array();

        $finder = new Finder();

        $iterator = $finder

            ->name('*.gz')

            ->files()

            ->depth(0)

            ->sortByModifiedTime()

            ->in($this->backupPath);



        foreach($iterator as $file)

        {

            $files[] = array(

                'name' => $file->getFilename(),

                'size' => $file->getSize(),

                'ctime' => $file->getCTime(),

            );

        }



        return $this->render('Backup/index', array(

            'files' => $files,

        ));

    }



    public function downloadAction($fileName)

    {

        $file = new \SplFileInfo($this->backupPath.'/'.basename($fileName));



        if(!$file->isFile())

        {

            throw $this->createNotFoundException(sprintf('File %s does not exists in %s', $fileName, $this->backupPath));

        }



        $response = new StreamedResponse();

        $response->setCallback(function() use ($file) {

            $file->openFile()->fpassthru();

        });

        $response->headers->set('X-Sendfile', $file->getFilename());

        $response->headers->set('Content-type', 'application/octet-stream');

        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $file->getFilename()));



        return $response;

    }



    public function deleteAction(Request $request, $fileName)

    {

        $file = new \SplFileInfo($this->backupPath.'/'.basename($fileName));



        if(!$file->isFile())

        {

            throw $this->createNotFoundException(sprintf('File %s does not exists in %s', $fileName, $this->backupPath));

        }



        if($request->isMethod('post'))

        {

            $filesystem = new Filesystem();

            $filesystem->remove($file->getRealPath());



            $this->getSession()->getFlashBag()->add('msg', sprintf('Plik <strong>%s</strong> został pomyślnie usunięty.', $file->getFilename()));



            return $this->redirect($this->generateUrl('backup_index'));

        }



        return $this->render('confirm', array(

            'msg' => sprintf('Czy jesteś pewny że chcesz usunać plik <strong>%s</strong> (plik kopii zapasowej)?', $file->getFilename()),

            'action' => $this->generateUrl('backup_delete', array('fileName' => $file->getFilename())),

        ));

    }

}