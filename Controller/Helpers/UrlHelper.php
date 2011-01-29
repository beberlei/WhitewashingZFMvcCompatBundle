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

namespace Whitewashing\Zend\Mvc1CompatBundle\Controller\Helpers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class UrlHelper extends Helper
{
    /**
     * @var Symfony\Component\Routing\Router
     */
    private $router;
    private $routeNameParser;
    private $httpKernel;

    public function __construct(RouterInterface $router, $routeNameParser, $kernel)
    {
        $this->router = $router;
        $this->routeNameParser = $routeNameParser;
        $this->httpKernel = $kernel;
    }

    public function simple($action, $controller = null, $module = null, array $params = array())
    {
        $zendRequest = $this->getActionController()->getRequest();
        if (!$controller) {
            $controller = $zendRequest->getControllerName();
        }
        if (!$module) {
            $module = $zendRequest->getModuleName();
        }

        /*$routes = $this->router->getRouteCollection()->all();
        foreach ($routes AS $route) {
            $details = $this->routeNameParser->parse($route->getDefault('_controller'));
        }*/

        $controller = sprintf('%sBundle:%s:%s',$module, $controller, $action);
        return $this->httpKernel->forward($controller, array(), $params);
    }

    public function getName()
    {
        return 'url';
    }

    public function direct($action, $controller = null, $module = null, array $params = array())
    {
        return $this->simple($action, $controller, $module, $params);
    }
}