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
use Whitewashing\Zend\Mvc1CompatBundle\Controller\ZendController;

class CoreViewListener
{
    private $viewBasePaths = array();
    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    public function __construct($viewBasePaths, $router)
    {
        $this->viewBasePaths = $viewBasePaths;
        $this->router = $router;
    }

    public function filterResponse(Event $event, $response)
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $event->get('request');
        if ($request->attributes->has('zend_compat_controller')) {
            /* @var $controller ZendController */
            $controller = $request->attributes->get('zend_compat_controller');
            $controllerName = $request->attributes->get('_controller');
            $controller->view->addBasePath();
        }
    }
}
