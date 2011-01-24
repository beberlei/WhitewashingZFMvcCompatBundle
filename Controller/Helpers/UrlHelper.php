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
     * @var Symfony\Component\Routing\RouterInterface
     */
    private $router;
    private $request;

    public function __construct(RouterInterface $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function simple($action, $controller = null, $module = null, array $params = null)
    {
        if (!$controller) {
            list($bundle, $controller) = explode(":", $this->request->attributes->get('_controller'));
        } else if (!$module) {
            list($bundle, $devnull) = explode(":", $this->request->attributes->get('_controller'));
        }

        $this->router->

        $controller = $module.":".$controller.":".$action;
        return $this->tools->container->get('http_kernel')->forward($controller, array(), $params);
    }
}