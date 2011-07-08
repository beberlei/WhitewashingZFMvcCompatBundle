<?php
/**
 * Whitewashing ZFMvcCompatBundle
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Whitewashing\ZFMvcCompatBundle\View;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;
use Whitewashing\ZFMvcCompatBundle\Controller\ZendController;

class CoreViewListener
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     *
     * @var Whitewashing\ZFMvcCompatBundle\View\View1
     */
    private $zendView;

    public function __construct($templating, $zendView)
    {
        $this->templating = $templating;
        $this->zendView = $zendView;
    }

    public function filterResponse(GetResponseForControllerResultEvent  $event)
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $event->getRequest();
        

        if ($request->attributes->has('zend_compat_controller') && !$event->hasResponse()) {
            /* @var $zendController ZendController */
            $zendController = $request->attributes->get('zend_compat_controller');
            $zendController->postDispatch();
            /* @var $zendRequest ZendRequest */
            $zendRequest = $zendController->getRequest();

            /* @var $response Symfony\Component\HttpFoundation\Response */
            $response = new Response();

            /* @var $zendResponse ZendResponse */
            $zendResponse = $zendController->getResponse();
            $response->headers->add($zendResponse->getHeaders());
            $response->setStatusCode($zendResponse->getHttpResponseCode());

            if ($zendController->getHelper('viewrenderer')->getNoRender() === false) {
                // TODO: "html" => ContextSwitch
                $viewName = sprintf("%sBundle:%s:%s.%s.%s",
                    $zendRequest->getModuleName(),
                    $zendRequest->getControllerName(),
                    $zendRequest->getActionName(),
                    "html", "phtml"
                );

                $vars = $zendController->view->allVars();
                foreach ($vars AS $k => $v) {
                    if ($v instanceof \Zend_Form) {
                        $v->setView($this->zendView);
                    }
                }

                $content = $this->templating->render($viewName, $vars);

                if ($zendController->getHelper('layout')->isEnabled()) {
                    $content = $this->templating->render($zendController->getHelper('layout')->getLayout(), array('content' => $content));
                }
                $response->setContent($content);
            }
            $event->setResponse($response);
        }
    }
}
