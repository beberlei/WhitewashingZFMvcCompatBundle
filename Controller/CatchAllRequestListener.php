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

use Symfony\Component\EventDispatcher\Event;

class CatchAllRequestListener
{
    private $parser;

    /**
     * @var array
     */
    private $enabledBundles = array();

    public function __construct(RouteNameParser $parser, $enabledBundles)
    {
        $this->parser = $parser;
        $this->enabledBundles = $enabledBundles;
    }

    public function resolve(Event $event)
    {
        $request = $event->get('request');

        if ($request->attributes->has('_controller')) {
            return;
        }

        $url = $request->getPathInfo();

        $parts = explode('/', $url);
        if (count($parts) < 4) {
            return;
        }

        $bundle = sprintf('%sBundle', $this->parser->formatModule($parts[1]));

        if (!in_array($bundle, $this->enabledBundles)) {
            return;
        }

        $controllerName = sprintf(
            '%s:%s:%s',
            $bundle,
            $this->parser->formatController($parts[2]),
            $parts[3]
        );

        $request->attributes->add(array('_controller'=> $controllerName));
    }
}