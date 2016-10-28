<?php


namespace Anime\Controller;


use Gregwar\Captcha\CaptchaBuilder;
use Sequence\Controller;
use Symfony\Component\HttpFoundation\Response;

class CaptchaController extends Controller
{
    public function generateAction()
    {
        $builder = new CaptchaBuilder();
        $builder->build($this->getRequest()->query->get('width', 250), $this->getRequest()->query->get('height', 67));

        $this->getSession()->set('captcha', $builder->getPhrase());

        return new Response($builder->get(), 200, array('Content-Type' => 'image/png'));
    }
} 