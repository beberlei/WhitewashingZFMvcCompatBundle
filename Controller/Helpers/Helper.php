<?php

namespace Whitewashing\Zend\Mvc1CompatBundle\Controller\Helpers;

use Whitewashing\Zend\Mvc1CompatBundle\Controller\ZendController;

/**
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Helper
{
    /**
     * $_actionController
     *
     * @var Zend_Controller_Action $_actionController
     */
    protected $_actionController = null;

    /**
     * setActionController()
     *
     * @param  ZendController $actionController
     * @return Helper
     */
    public function setActionController(ZendController $actionController = null)
    {
        $this->_actionController = $actionController;
        return $this;
    }

    /**
     * Retrieve current action controller
     *
     * @return Zend_Controller_Action
     */
    public function getActionController()
    {
        return $this->_actionController;
    }

    /**
     * Hook into action controller initialization
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Hook into action controller preDispatch() workflow
     *
     * @return void
     */
    public function preDispatch()
    {
    }

    /**
     * Hook into action controller postDispatch() workflow
     *
     * @return void
     */
    public function postDispatch()
    {
    }

    /**
     * getRequest() -
     *
     * @return Zend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        return $this->getActionController()->getRequest();
    }

    /**
     * getResponse() -
     *
     * @return Zend_Controller_Response_Abstract $response
     */
    public function getResponse()
    {
        return $this->getActionController()->getResponse();
    }

    /**
     * getName()
     *
     * @return string
     */
    abstract public function getName();

    public function __call($method, $args)
    {
        throw new \BadMethodCallException("$method() is not supported.");
    }
}