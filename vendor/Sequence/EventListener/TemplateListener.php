<?php


namespace Sequence\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Templating\PhpEngine;

class TemplateListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Templating\PhpEngine
     */
    protected $templating;

    /**
     * @param PhpEngine $templating
     */
    public function __construct(PhpEngine $templating)
    {
        $this->templating = $templating;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if(!is_array($controller = $event->getController()))
        {
            return;
        }

        $request = $event->getRequest();

        $request->attributes->set('_template', $this->guessTemplateName($controller));
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $parameters = $event->getControllerResult();

        if(!is_array($parameters))
        {
            return $parameters;
        }

        if(!$template = $request->attributes->get('_template'))
        {
            return $parameters;
        }

        if(null === $response = $event->getResponse())
        {
            $response = new Response();
        }

        $response->setContent($this->templating->render($template, $parameters));

        $event->setResponse($response);
    }

    /**
     * @param array $controller
     * @return string
     * @throws \InvalidArgumentException
     */
    public function guessTemplateName($controller)
    {
        $className = get_class($controller[0]);

        if (!preg_match('/Controller\\\(.+)Controller$/', $className, $matchController))
        {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like a controller class (it must be in a "Controller" sub-namespace and the class name must end with "Controller")', get_class($controller[0])));
        }

        if (!preg_match('/^(.+)Action$/', $controller[1], $matchAction))
        {
            throw new \InvalidArgumentException(sprintf('The "%s" method does not look like an action method (it does not end with Action)', $controller[1]));
        }

        $controller = str_replace('\\', '/', $matchController[1]);

        return $controller.DIRECTORY_SEPARATOR.$matchAction[1];
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array('onKernelController', -128),
            KernelEvents::VIEW => 'onKernelView',
        );
    }
}