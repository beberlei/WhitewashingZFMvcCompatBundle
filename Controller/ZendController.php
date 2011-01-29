<?php
/**
 * Whitewashing ZendMvc1Bundle
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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Whitewashing\Zend\Mvc1CompatBundle\View\ParameterBag;
use Whitewashing\Zend\Mvc1CompatBundle\Controller\Helpers\HelperBroker;

abstract class ZendController implements ContainerAwareInterface
{
    protected $container;
    protected $_request;
    protected $_response;

    /**
     * The symfony request object.
     *
     * @var Request
     */
    private $request;

    /**
     * @var ParameterBag
     */
    public $view;

    /**
     * @var HelperBroker
     */
    protected $_helper;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->request = $container->get('request');
        $this->request->attributes->set('zend_compat_controller', $this);
        $this->_request = $this->container->get('whitewashing.zend.mvc1compat.controller.request');
        $this->_response = new ZendResponse();
        $this->view = new ParameterBag();
        $this->_helper = new HelperBroker($this->container, $this);

        $this->init();
        $this->preDispatch();
    }

    public function init() {}

    public function preDispatch() {}

    public function postDispatch() {}

    /**
     * @param  string $name
     * @return Helper
     */
    public function getHelper($name)
    {
        return $this->_helper->getHelper($name);
    }

    protected function _getParam($name, $default = null)
    {
        $value = $this->_request->getParam($name);
        if ((null === $value || '' === $value) && (null !== $default)) {
            $value = $default;
        }

        return $value;
    }

    protected function _setParam($name, $value)
    {
        $this->_request->setParam($name, $value);
        return $this;
    }

    protected function _hasParam($name)
    {
        return null !== $this->_request->getParam($paramName);
    }

    protected function _getAllParams()
    {
        return $this->_request->getParams();
    }

    /**
     * @return ZendRequest
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return ZendResponse
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Forward to another controller/action.
     *
     * It is important to supply the unformatted names, i.e. "article"
     * rather than "ArticleController".  The dispatcher will do the
     * appropriate formatting when the request is received.
     *
     * If only an action name is provided, forwards to that action in this
     * controller.
     *
     * If an action and controller are specified, forwards to that action and
     * controller in this module.
     *
     * Specifying an action, controller, and module is the most specific way to
     * forward.
     *
     * A fourth argument, $params, will be used to set the request parameters.
     * If either the controller or module are unnecessary for forwarding,
     * simply pass null values for them before specifying the parameters.
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return void
     */
    final protected function _forward($action, $controller = null, $module = null, array $params = null)
    {
        if (!$controller) {
            list($bundle, $controller) = explode(":", $this->request->attributes->get('_controller'));
        } else if (!$module) {
            list($bundle, $devnull) = explode(":", $this->request->attributes->get('_controller'));
        }

        $controller = $module.":".$controller.":".$action;
        return $this->container->get('http_kernel')->forward($controller, array(), $params);
    }

    /**
     * Redirect to another URL
     *
     * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
     *
     * @param string $url
     * @param array $options Options to be used when redirecting
     * @return void
     */
    protected function _redirect($url, array $options = array())
    {
        $response = $this->container->get('response');
        $response->setRedirect($url);
        return $response;
    }
}
