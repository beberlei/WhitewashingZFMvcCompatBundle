<?php

namespace Whitewashing\ZFMvcCompatBundle\Tests\Router\Loader;

use Whitewashing\ZFMvcCompatBundle\Router\Loader\ZFRouterLoader;

class ZFRouterLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testIniFile()
    {
        $locator = new \Symfony\Component\Config\FileLocator(__DIR__ . "/fixtures");

        $loader = new ZFRouterLoader($locator);
        $collection = $loader->load("routes.ini");

        $this->assertInstanceOf('Symfony\Component\Routing\Route', $collection->get('archive'));
        $this->assertInstanceOf('Symfony\Component\Routing\Route', $collection->get('news'));
    }
}