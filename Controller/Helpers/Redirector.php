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

namespace Whitewashing\ZFMvcCompatBundle\Controller\Helpers;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Redirector extends Helper
{
    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @var bool
     */
    protected $useAbsoluteUri = true;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    public function getName()
    {
        return "redirector";
    }

    /**
     * Return use absolute URI flag
     *
     * @return boolean
     */
    public function getUseAbsoluteUri()
    {
        return $this->useAbsoluteUri;
    }

    /**
     * Set use absolute URI flag
     *
     * @param  boolean $flag
     * @return Zend_Controller_Action_Helper_Redirector Provides a fluent interface
     */
    public function setUseAbsoluteUri($flag = true)
    {
        $this->useAbsoluteUri = ($flag) ? true : false;
        return $this;
    }

    public function setGotoSimple($action, $controller = null, $module = null, array $params = array())
    {
        $this->response = new RedirectResponse($this->urlHelper->direct($action, $controller, $module, $params));
    }

    public function setGotoRoute(array $urlOptions = array(), $name = null, $absolute = true)
    {
        $this->response = new RedirectResponse($this->urlHelper->url($urlOptions, $name, $absolute || $this->useAbsoluteUri));
    }

    public function setGotoUrl($url, array $options = array())
    {
        if (!isset($options['code'])) {
            $options['code'] = null;
        }
        $this->response = new RedirectResponse($url, $options['code']);
    }

    public function gotoSimple($action, $controller = null, $module = null, array $params = array())
    {
        $this->setGotoSimple($action, $controller, $module, $params);
        return $this->response;
    }

    public function gotoSimpleAndExit($action, $controller = null, $module = null, array $params = array())
    {
        $this->setGotoSimple($action, $controller, $module, $params);
        return $this->response;
    }

    public function gotoRoute(array $urlOptions = array(), $name = null, $absolute = true)
    {
        $this->setGotoRoute($urlOptions, $name, $absolute || $this->useAbsoluteUri);
        return $this->response;
    }

    public function gotoRouteAndExit(array $urlOptions = array(), $absolute = true)
    {
        $this->setGotoRoute($urlOptions, $name, $absolute || $this->useAbsoluteUri);
        return $this->response;
    }

    public function gotoUrl($url, array $options = array())
    {
        $this->setGotoUrl($url, $options);
        return $this->response;
    }

    public function gotoUrlAndExit($url, array $options = array())
    {
        $this->setGotoUrl($url, $options);
        return $this->response;
    }

    public function direct($action, $controller = null, $module = null, array $params = array())
    {
        return $this->gotoSimple($action, $controller, $module, $params);
    }
}