<?php

namespace Whitewashing\ZFMvcCompatBundle\Router\Loader;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class ZFRouterLoader extends FileLoader
{
    public function supports($resource, $type = null)
    {
        $supported = array('ini', 'php', 'xml', 'zfmvc');

        return is_string($resource) && in_array(pathinfo($resource, PATHINFO_EXTENSION), $supported) && (!$type || in_array($type, $supported));
    }

    public function load($file, $type = null)
    {
        $file = $this->locator->locate($file);
        $type = $type ?: pathinfo($file, PATHINFO_EXTENSION);
        switch($type) {
            case 'php':
                $data = require($file);
                $config = new \Zend_Config($data);
                break;
            case 'xml':
                $config = new \Zend_Config_Xml($file);
                break;
            case 'ini':
                $config = new \Zend_Config_Ini($file, "routes");
                break;
        }
        $data = $config->toArray();
        if (isset($data['routes'])) {
            $data = $data['routes'];
        }

        $collection = new RouteCollection;
        foreach ($data AS $routeName => $config) {
            if (isset($config['type']) && $config['type'] == "Zend_Controller_Router_Route_Regex") {
                throw new \InvalidArgumentException("Not supported");
            }

            if (!isset($config['reqs'])) {
                $config['reqs'] = array();
            }
            if (!isset($config['defaults'])) {
                $config['defaults'] = array();
            }
            if (!isset($config['options'])) {
                $config['options'] = array();
            }

            if (!isset($config['defaults']['module'])) {
                // TODO: DefaultModule config
                $config['defaults']['module'] = 'Default';
            }
            if (!isset($config['defaults']['controller'])) {
                $config['defaults']['controller'] = 'Index';
            }
            if (!isset($config['defaults']['action'])) {
                $config['defaults']['action'] = 'index';
            }
            $config['defaults']['_controller'] = sprintf('%sBundle:%s:%s',
                $config['defaults']['module'],
                $config['defaults']['controller'],
                $config['defaults']['action']
            );

            if (preg_match_all('(:([^/]+)+)', $config['route'], $matches)) {
                for ($i = 0; $i < count($matches[0]); $i++) {
                    $config['route'] = str_replace($matches[0][$i], "{" . $matches[1][$i] . "}", $config['route']);
                }
            }

            $route = new Route($config['route'], $config['defaults'], $config['reqs'], $config['options']);
            $collection->add($routeName, $route);
        }

        return $collection;
    }
}
