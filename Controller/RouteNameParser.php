<?php
/*
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

namespace Whitewashing\Zend\Mvc1CompatBundle\Controller;

use Symfony\Component\HttpKernel\KernelInterface;

class RouteNameParser
{
    private $cache = array();

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function parse($compiledControllerName)
    {
        if (isset($this->cache[$compiledControllerName])) {
            return $this->cache[$compiledControllerName];
        }

        // skip controllers as services
        if (strpos($compiledControllerName, "::") === false) {
            return array();
        }

        $details = array();
        list($controllerName, $actionName) = explode("::", $compiledControllerName);
        $details['action'] = str_replace("Action", "", $actionName);
        $controllerRefl = new \ReflectionClass($controllerName);
        $details['controller'] = str_replace("Controller", "", $controllerRefl->getShortName());
        $controllerNamespace = $controllerRefl->getNamespaceName();

        foreach ($this->kernel->getBundles() AS $bundle) {
            if (strpos($controllerNamespace, $bundle->getNamespace()) === 0) {
                $details['module'] = str_replace("Bundle", "", $bundle->getName());
                break;
            }
        }

        return $this->cache[$compiledControllerName] = $details;
    }
}