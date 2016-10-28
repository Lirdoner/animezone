<?php


namespace Anime\Controller;


use Symfony\Component\HttpFoundation\Response;
use Sequence\Controller;

class SecurityController extends Controller
{
    public function statisticsAction()
    {
        if(!$this->getSession()->has('secure_image'))
        {
            $this->getSession()->set('secure_image', true);
        }

        $image = base64_decode('R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw==');

        return new Response($image, 200, array('Content-Type' => 'image/gif'));
    }
} 