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

    /**
     * @var Request
     */
    private $request;

    public function __construct(RouterInterface $router, $routeNameParser, $request)
    {
        $this->router = $router;
        $this->routeNameParser = $routeNameParser;
        $this->request = $request;
    }

    public function simple($action, $controller = null, $module = null, array $params = array())
    {
        $zendRequest = $this->getActionController()->getRequest();
        if (!$controller) {
            $controller = $zendRequest->getControllerName();
        } else {
            $controller = $this->routeNameParser->formatController($controller);
        }
        if (!$module) {
            $module = $zendRequest->getModuleName();
        } else {
            $module = $this->routeNameParser->formatModule($module);
        }

        $routes = $this->router->getRouteCollection()->all();
        foreach ($routes AS $route) {
            $details = $this->routeNameParser->parse($route->getDefault('_controller'));
            if (!isset($details['module']) || !isset($details['controller']) || !isset($details['action'])) {
                continue;
            }

            $defaults = $route->getDefaults();
            if ($module == $details['module'] &&
                $controller == $details['controller'] &&
                $action == $details['action'] &&
                count(array_intersect_key($defaults, $params)) == (count($defaults)-1) ) {
                
                /* @var $pattern Route */
                $pattern = $route->getPattern();
                foreach ($params AS $name => $value) {
                    $pattern = str_replace("{$name}", $value, $pattern);
                }

                return $this->request->getUriForPath($pattern);
            }
        }
        throw new \RuntimeException("Did not find a route matching the given module/controller/actions pair.");
    }

    public function url($urlOptions = array(), $name = null, $absolute = false)
    {
        return $this->router->generate($name, $urlOptions, $absolute);
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