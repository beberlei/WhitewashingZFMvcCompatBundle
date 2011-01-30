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

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

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
class ZendViewEngine implements EngineInterface
{
    /**
     * @var TemplateLocatorInterface
     */
    private $locator;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TemplateNameParserInterface
     */
    private $parser;

    /**
     * @var Zend_View_Interface
     */
    private $view;

    /**
     * @var array
     */
    private $cache = array();

    /**
     * @param TemplateLocatorInterface $locator
     * @param ContainerInterface $container
     * @param TemplateNameParserInterface $parser
     * @param Zend_View_Interface $zendView
     */
    public function __construct(TemplateLocatorInterface $locator, ContainerInterface $container, TemplateNameParserInterface $parser, $zendView)
    {
        $this->locator = $locator;
        $this->container = $container;
        $this->parser = $parser;
        $this->view = $zendView;
        // Zend View is not able to handle absolute paths except with this little trick
        $this->view->setScriptPath('');
    }

    public function exists($name)
    {
        return (file_exists($this->findTemplate($name)));
    }

    public function load($name)
    {
        return $this->findTemplate($name);
    }

    public function render($name, array $parameters = array())
    {
        $templateName = $this->load($name);
        $view = clone $this->view;
        $view->assign($parameters);
        return $view->render($templateName);
    }

    /**
     * Renders a view and returns a Response.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A Response instance
     *
     * @return Response A Response instance
     */
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = $this->container->get('response');
        }

        $response->setContent($this->render($view, $parameters));

        return $response;
    }

    public function supports($name)
    {
        $template = $this->parser->parse($name);
        return 'phtml' === $template['engine'];
    }

    protected function findTemplate($name)
    {
        if (!is_array($name)) {
            $name = $this->parser->parse($name);
        }

        $key = md5(serialize($name));
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        if (false == $file = $this->locator->locate($name)) {
            throw new \RuntimeException(sprintf('Unable to find template "%s".', json_encode($name)));
        }

        return $this->cache[$key] = $file;
    }
}
