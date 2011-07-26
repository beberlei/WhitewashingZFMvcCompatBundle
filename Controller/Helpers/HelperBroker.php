<?php
/**
 * Whitewashing Zendmvc1compat
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Whitewashing\ZFMvcCompatBundle\Controller\Helpers;

use Symfony\Component\DependencyInjection\Container;
use Whitewashing\ZFMvcCompatBundle\Controller\ZendController;

class HelperBroker
{
    private $helpers = array();

    public function __construct(Container $container, ZendController $controller)
    {
        // TODO: Convert to using tags!
        // TODO: Remember helpers should be scope=request
        $helpers = array(
            'whitewashing.zfmvccompat.actionhelper.flashmessenger',
            'whitewashing.zfmvccompat.actionhelper.redirector',
            'whitewashing.zfmvccompat.actionhelper.url',
            'whitewashing.zfmvccompat.actionhelper.contextswitch',
            'whitewashing.zfmvccompat.actionhelper.layout',
            'whitewashing.zfmvccompat.actionhelper.viewrenderer',
        );
        foreach ($helpers AS $helper) {
            $helper = $container->get($helper);
            $helper->setActionController($controller);
            $this->helpers[$helper->getName()] = $helper;
        }
    }

    public function __get($helper)
    {
        return $this->getHelper($helper);
    }
    
    public function getHelper($name)
    {
        $name = strtolower($name);
        if (!isset($this->helpers[$name])) {
            throw new \RuntimeException("No Zend ActionHelper with name $name registered.");
        }
        return $this->helpers[$name];
    }

    public function __call($method, $args)
    {
        $helper = $this->getHelper($method);
        return call_user_func_array(array($helper, 'direct'), $args);
    }
}