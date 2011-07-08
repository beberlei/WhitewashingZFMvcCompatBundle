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

namespace Whitewashing\ZFMvcCompatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class ZendRequest
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Allowed parameter sources
     * @var array
     */
    protected $paramSources = array('_GET', '_POST');

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var RouteNameParser
     */
    private $parser;

    private $actionName;
    private $controllerName;
    private $moduleName;

    public function __construct(Request $request, RouteNameParser $parser)
    {
        $this->request = $request;
        $this->parser = $parser;
    }

    private function parseRequest()
    {
        if ($this->actionName) {
            return;
        }

        $details = $this->parser->parse($this->request->attributes->get('_controller'));
        $this->actionName = $details['action'];
        $this->controllerName = $details['controller'];
        $this->moduleName = $details['module'];
    }

    public function __get($name)
    {
        if ($this->request->attributes->has($name)) {
            return $this->request->attributes->get($name);
        } else if ($this->request->query->has($name)) {
            return $this->request->query->get($name);
        } else if ($this->request->request->has($name)) {
            return $this->request->request->get($name);
        } else if ($this->request->cookies->has($name)) {
            return $this->request->cookies->get($name);
        } else if ($this->request->server->has($name)) {
            return $this->request->server->get($name);
        } else {
            return null;
        }
    }

    public function get($name)
    {
        return $this->__get($name);
    }

    public function set($name, $value)
    {
        throw new \BadMethodCallException("Cannot set value, please use setParam()");
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function has($name)
    {
        if (isset($this->request->attributes[$name])) {
            return true;
        } else if (isset($this->request->query[$name])) {
            return true;
        } else if (isset($this->request->request[$name])) {
            return true;
        } else if (isset($this->request->cookies[$name])) {
            return true;
        } else if (isset($this->request->server[$name])) {
            return true;
        } else {
            return false;
        }
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function getQuery($name = null, $default = null)
    {
        if (null === $name) {
            return $this->request->query->all();
        }

        return (isset($this->request->query[$name])) ? $this->request->query[$name] : $default;
    }

    public function getPost($name = null, $default = null)
    {
        if (null === $name) {
            return $this->request->request->all();
        }

        return (isset($this->request->request[$name])) ? $this->request->request[$name] : $default;
    }

    public function getCookie($name = null, $default = null)
    {
        if (null === $name) {
            return $this->request->cookies->all();
        }

        return (isset($this->request->cookies[$name])) ? $this->request->cookies[$name] : $default;
    }

    public function getServer($name = null, $default = null)
    {
        if (null === $name) {
            return $this->request->server->all();
        }

        return (isset($this->request->server[$name])) ? $this->request->server[$name] : $default;
    }

    public function getEnv($key = null, $default = null)
    {
        if (null === $key) {
            return $_ENV;
        }

        return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
    }

    public function getRequestUri()
    {
        return $this->request->getRequestUri();
    }

    public function getBaseUrl()
    {
        return $this->request->getBaseUrl();
    }

    public function getBasePath()
    {
        return $this->request->getBasePath();
    }

    public function getPathInfo()
    {
        return $this->request->getPathInfo();
    }

    /**
     * Set allowed parameter sources
     *
     * Can be empty array, or contain one or more of '_GET' or '_POST'.
     *
     * @param  array $paramSoures
     * @return Zend_Controller_Request_Http
     */
    public function setParamSources(array $paramSources = array())
    {
        $this->paramSources = $paramSources;
        return $this;
    }

    /**
     * Get list of allowed parameter sources
     *
     * @return array
     */
    public function getParamSources()
    {
        return $this->paramSources;
    }

    public function getParam($key, $default = null)
    {
        $paramSources = $this->getParamSources();
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        } else if ($this->request->attributes->has($key)) {
            return $this->request->attributes->get($key);
        } elseif (in_array('_GET', $paramSources) && $this->request->query->has($key)) {
            return $this->request->query->get($key);
        } elseif (in_array('_POST', $paramSources) && $this->request->request->has($key)) {
            return $this->request->request->get($key);
        }

        return $default;
    }

    public function getParams()
    {
        $return       = $this->params;
        $paramSources = $this->getParamSources();
        if (in_array('_GET', $paramSources)) {
            $return += $this->request->query->all();
        }
        if (in_array('_POST', $paramSources)) {
            $return += $this->request->request->all();
        }
        return $return;
    }

    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
        return $this;
    }

    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function getMethod()
    {
        return $this->request->getMethod();
    }

    public function isPost()
    {
        return ($this->request->getMethod() == 'POST');
    }

    public function isGet()
    {
        return ($this->request->getMethod() == 'GET');
    }

    public function isPut()
    {
        return ($this->request->getMethod() == 'PUT');
    }

    public function isDelete()
    {
        return ($this->request->getMethod() == 'DELETE');
    }

    public function isOptions()
    {
        return ($this->request->getMethod() == 'OPTIONS');
    }

    public function isHead()
    {
        return ($this->request->getMethod() == 'HEAD');
    }

    public function isXmlHttpRequest()
    {
        return $this->request->isXmlHttpRequest();
    }

    public function isFlashRequest()
    {
        $header = strtolower($this->request->headers->get('USER_AGENT'));
        return (strstr($header, ' flash')) ? true : false;
    }

    public function isSecure()
    {
        return $this->request->isSecure();
    }

    public function getHeader($name)
    {
        return $this->request->headers->get($name);
    }

    public function getScheme()
    {
        return $this->request->getScheme();
    }

    public function getHttpHost()
    {
        return $this->request->getHttpHost();
    }

    public function getClientIp($proxy)
    {
        return $this->request->getClientIp($proxy);
    }

    public function getModuleName()
    {
        $this->parseRequest();
        return $this->moduleName;
    }

    public function getControllerName()
    {
        $this->parseRequest();
        return $this->controllerName;
    }

    public function getActionName()
    {
        $this->parseRequest();
        return $this->actionName;
    }

    public function getUserParams()
    {
        return $this->params;
    }

    public function getUserParam($key, $default = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return $default;
    }

    public function clearParams()
    {
        $this->params = array();
        return $this;
    }
}
