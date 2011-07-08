<?php
/*
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

namespace Whitewashing\ZFMvcCompatBundle\Controller;

use Symfony\Component\HttpKernel\KernelInterface;

class RouteNameParser
{
    private $cache = array();

    /**
     * @var KernelInterface
     */
    private $kernel;

    private $moduleCache = array();

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function parse($symfonyControllerName)
    {
        if (isset($this->cache[$symfonyControllerName])) {
            return $this->cache[$symfonyControllerName];
        }

        // skip controllers as services
        if (strpos($symfonyControllerName, ".") !== false) {
            return array();
        }

        if (substr_count($symfonyControllerName, "::") == 1) {
            $details = array();
            list($controllerName, $actionName) = explode("::", $symfonyControllerName);
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
        } else {
            list($module, $controller, $action) = explode(":", $symfonyControllerName);
            $details['action'] = $action;
            $details['controller'] = $controller;
            $details['module'] = str_replace("Bundle", "", $module);
        }

        return $this->cache[$symfonyControllerName] = $details;
    }

    /**
     * Get correct casing of the module which is based on the bundle.
     *
     * @param  string $module
     * @return string
     */
    public function formatModule($module)
    {
        $module = strtolower($module);
        if (isset($this->moduleCache[$module])) {
            return $this->moduleCache[$module];
        }

        foreach ($this->kernel->getBundles() AS $bundle) {
            if ($module."bundle" == strtolower($bundle->getName())) {
                return $this->moduleCache[$module] = str_replace("Bundle", "", $bundle->getName());
            }
        }
        throw new \RuntimeException("Couldnt find a matching bundle for the module $module");
    }

    public function formatController($controller)
    {
        return ucfirst($controller);
    }
}