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

namespace Whitewashing\ZFMvcCompatBundle\Tests\Controller;

use Whitewashing\ZFMvcCompatBundle\Controller\RouteNameParser;

class RouteNameParserTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;

    public function setUp()
    {
        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $this->parser = new RouteNameParser($this->kernel);
    }

    public function testNotSupportsServices()
    {
        $this->assertEquals(array(), $this->parser->parse('fos_user_controller.actionName'));
    }

    public function testSupportsUncompiledSyntax()
    {
        $this->assertEquals(
            array("module" => "Hello", "controller" => "Test", "action" => "index"),
            $this->parser->parse("HelloBundle:Test:index")
        );
    }

    public function testSupportsCompiledSyntax()
    {
        $bundle = $this->getMock('WhitewashingCompatCompiledBundleMock', array('getName', 'getNamespace'));
        $bundle->expects($this->at(0))
               ->method('getNamespace')
               ->will($this->returnValue('Whitewashing\ZFMvcCompatBundle'));
        $bundle->expects($this->at(1))
               ->method('getName')
               ->will($this->returnValue('HelloBundle'));
        
        $this->kernel->expects($this->once())
                     ->method('getBundles')
                     ->will($this->returnValue(array($bundle)));

        $this->assertEquals(
            array("module" => "Hello", "controller" => "Test", "action" => "index"),
            $this->parser->parse("Whitewashing\\ZFMvcCompatBundle\\Tests\\Controller\\TestController::indexAction")
        );

        // cached?
        $this->assertEquals(
            array("module" => "Hello", "controller" => "Test", "action" => "index"),
            $this->parser->parse("Whitewashing\\ZFMvcCompatBundle\\Tests\\Controller\\TestController::indexAction")
        );
    }

    public function testFormatModule()
    {
        $bundle = $this->getMock('WhitewashingCompatFormatBundleMock', array('getName', 'getNamespace'));
        $bundle->expects($this->any())
               ->method('getName')
               ->will($this->returnValue('HeLLoBundle'));

        $this->kernel->expects($this->once())
                     ->method('getBundles')
                     ->will($this->returnValue(array($bundle)));

        $this->assertEquals('HeLLo', $this->parser->formatModule('hello'));
    }

    public function testFormatController()
    {
        $this->assertEquals('Test', $this->parser->formatController('test'));
    }
}

class TestController
{
    
}