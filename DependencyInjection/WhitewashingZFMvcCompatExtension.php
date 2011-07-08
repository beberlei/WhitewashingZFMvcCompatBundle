<?php

/**
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

namespace Whitewashing\ZFMvcCompatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Resource\FileResource;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class WhitewashingZFMvcCompatExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('compat.xml');

        foreach ($configs AS $config) {
            if (isset($config['default_layout_resource'])) {
                $container->setParameter(
                    'whitewashing.zfmvccompat.default_layout_resource',
                    $config['default_layout_resource']
                );
            }
            if (isset($config['catchall_bundles'])) {
                $container->setParameter(
                    'whitewashing.zfmvccompat.catchall_bundles',
                    $config['catchall_bundles']
                );
            }
            if (isset($config['db_conn'])) {
                $def = new Definition('Zend_Db_Adapter_Abstract');
                $def->setFactoryClass('Zend_Db');
                $def->setFactoryMethod('factory');
                $def->setArguments(array($config['db_conn']['adapter'], $config['db_conn']['params']));

                $container->setDefinition('whitewashing.zfmvcompat.db', $def);
            }
        }
    }

    public function getAlias()
    {
        return 'whitewashing_zf_mvc_compat';
    }
}