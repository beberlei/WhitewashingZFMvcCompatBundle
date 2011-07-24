<?php

namespace Whitewashing\ZFMvcCompatBundle\Tests\View;

use Whitewashing\ZFMvcCompatBundle\View\ZendViewEngine;

class ZendViewEngineTest extends \PHPUnit_Framework_TestCase
{
    private $locator;
    private $container;
    private $nameParser;
    private $view;
    private $engine;

    public function setUp()
    {
        $this->locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->nameParser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $this->view = $this->getMock('Zend_View_Interface');
        $this->engine = new ZendViewEngine($this->locator, $this->container, $this->nameParser, $this->view);
    }

    public function testSupports()
    {
        $templateResource = 'HelloBundle:test:index.html.phtml';

        $this->nameParser->expects($this->once())->method('parse')
                         ->with($this->equalTo($templateResource))
                         ->will($this->returnValue(array('engine' => 'phtml')));

        $this->assertTrue($this->engine->supports($templateResource));
    }

    public function testLoad()
    {
        $templateResource = 'HelloBundle:test:index.html.phtml';
        $parsedTemplate = array(
            'engine' => 'phtml', 'format' => 'html',
            'bundle' => 'HelloBundle', 'controller' => 'test', 'action' => 'index'
        );
        $templatePath = '/template.path';

        $this->nameParser->expects($this->exactly(2))->method('parse')
                         ->with($this->equalTo($templateResource))
                         ->will($this->returnValue($parsedTemplate));

        $this->locator->expects($this->once())->method('locate')
                      ->with($this->equalTo($parsedTemplate))
                      ->will($this->returnValue($templatePath));

        $this->assertEquals($templatePath, $this->engine->load($templateResource));
        $this->assertEquals($templatePath, $this->engine->load($templateResource));
    }
}