<?php
/*
 * Whitewashing ZendMvc1CompatLayer
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Whitewashing\ZFMvcCompatBundle\Controller\RouteNameParser;

class View1 extends \Zend_View
{
    private $container;
    private $parser;

    public function __construct(ContainerInterface $container, RouteNameParser $parser)
    {
        $this->container = $container;
        $this->parser = $parser;
    }

    public function action($action, $controller, $module, array $params = array())
    {
        $options['attributes'] = $params;

        $symfonyController = sprintf('%sBundle:%s:%s',
            $this->parser->formatModule($module), $this->parser->formatController($controller), $action
        );
        return $this->container->get('http_kernel')->render($symfonyController, $options);
    }

    public function baseUrl()
    {
        return $this->container->get('request')->getUriForPath('/');
    }

    public function partial($resource, array $params = array())
    {
        return $this->container->get('templating')->render($resource, $params);
    }

    public function partialLoop($resource, array $models = array())
    {
        $html = '';
        foreach ($models AS $model) {
            $html .= $this->container->get('templating')->render($resource, $model);
        }
        return $html;
    }

    public function url(array $urlOptions = array(), $name = null, $absolute = false)
    {
        return $this->container->get('whitewashing.zfmvccompat.actionhelper.url')->url($urlOptions, $name, $absolute);
    }

    public function flashMessenger()
    {
        $flashMessenger = $this->container->get('whitewashing.zfmvccompat.actionhelper.flashmessenger');

        //get messages from previous requests
        $messages = $flashMessenger->getMessages();

        //add any messages from this request
        if ($flashMessenger->hasCurrentMessages()) {
            $messages = array_merge($messages, $flashMessenger->getCurrentMessages());
            //we don't need to display them twice.
            $flashMessenger->clearCurrentMessages();
        }

        return $messages;
    }
}