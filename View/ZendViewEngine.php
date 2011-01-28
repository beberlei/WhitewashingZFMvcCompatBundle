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

/**
 * Symfony Templating Engine that makes Zend View behave like the Symfony Templating layer
 *
 * What is necessary:
 * 1. For each Zend Mvc1 Compat Bundle there must be a scripts folder defined.
 * 2. Zend convention is to use the scripts folder of the module. A module is a bundle in symfony speak.
 * 3. When rendering detect the bundle, pick the scripts folder set it, render the "controller/action.format.ext".
 * 4. Get the layout from somewhere and render that also.
 *
 * All the template names passed in are already sticked toegether by the CoreViewListener which
 * has access to the controller helpers for example the ContextHelper.
 */
class ZendViewEngine implements \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
{
    /**
     * @var Zend_View_Interface
     */
    private $view;

    public function setView($view)
    {
        $this->view = $view;
    }

    private function parseTemplate($name)
    {
        list($bundle, $controller, $action) = explode(":", $name);
        list($action, $format, $ext) = explode(".", $action);

        return array(
            'bundle' => $bundle,
            'controller' => $controller,
            'action' => $action,
            'format' => $format,
            'ext' => $ext,
        );
    }

    public function exists($name)
    {
        $template = $this->parseTemplate($name);
    }

    public function load($name)
    {
        
    }

    public function render($name, array $parameters = array())
    {

    }

    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        
    }

    public function supports($name)
    {

    }
}
