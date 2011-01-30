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

namespace Whitewashing\Zend\Mvc1CompatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Resource\FileResource;

class Mvc1CompatExtension extends Extension
{
    public function compatLoad(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('compat.xml');

        foreach ($configs AS $config) {
            if (isset($config['default_layout_resource'])) {
                $container->setParameter(
                    'whitewashing.zend.mvc1compat.default_layout_resource',
                    $config['default_layout_resource']
                );
            }
            if (isset($config['catchall_bundles'])) {
                $container->setParameter(
                    'whitewashing.zend.mvc1compat.catchall_bundles',
                    $config['catchall_bundles']
                );
            }
        }
    }

    public function getAlias()
    {
        return 'zendmvc1';
    }

    public function getNamespace()
    {
        return 'http://www.whitewashing.de/symfony/schema/zend/mvc1compat.xsd';
    }

    public function getXsdValidationBasePath()
    {
        return false;
    }
}