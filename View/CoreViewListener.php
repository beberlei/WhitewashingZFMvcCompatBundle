<?php
/**
 * Whitewashing ZendMvc1CompatBundle
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Whitewashing\Zend\Mvc1CompatBundle\View;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\KernelInterface;
use Whitewashing\Zend\Mvc1CompatBundle\Controller\ZendController;

class CoreViewListener
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     * @var Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    public function __construct($templating, KernelInterface $kernel)
    {
        $this->templating = $templating;
        $this->kernel = $kernel;
    }

    public function filterResponse(Event $event, $response)
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $event->get('request');
        if ($request->attributes->has('zend_compat_controller') && !$response) {
            /* @var $zendController ZendController */
            $zendController = $request->attributes->get('zend_compat_controller');
            $zendController->postDispatch();
            
            /* @var $zendRequest ZendRequest */
            $zendRequest = $zendController->getRequest();

            /* @var $response Symfony\Component\HttpFoundation\Response */
            $response = $this->kernel->getContainer()->get('response');

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
                $content = $this->templating->render($viewName, $zendController->view->allVars());

                if ($zendController->getHelper('layout')->isEnabled()) {
                    $content = $this->templating->render($zendController->getHelper('layout')->getLayout(), array('content' => $content));
                }
                $response->setContent($content);
            }
        }
        return $response;
    }
}
