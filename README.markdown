# Symfony2 Zend MVC 1.x Compatibility Bundle

Simplifies moving your Zend 1.x MVC apps to Symfony 2 if you follow the way I interpreted the Zend project guidelines closely enough :-)

## What it can do:

* Has a base controller that mimics Zend_Controller_Action functionality
* Uses Zend_View as template engine and just replaces certain view helpers
* Ports some action helpers or implements proxies that implement Symfony2 functionality.

## What it cannot do (yet! (Waiting for your pull requests))

* Re-implement the Controller Plugin cycle
* Convert ZF routing config arrays to symfony2 ones

## What it will never do

* Make Zend Application code reusable (Use the dependency injection container)

## Semantic differences

### FrontController

Every code statically referencing the FrontController WILL fail. There is
no such thing as a static/singleton front controller. Get the resources you
need through the DIC.

### Request

* Zend_Controller_Request_Http::__get does check for $_ENV and the PATH_INFO or REQUEST_URI keys.
* Zend_Controller_Request_Http Aliases for params are not supported
* Zend_Controller_Request_Http setters are not implemented

### Response

* setHeader(), getHeader(), clearHeader() dont normalize the header key.
* setRawHeader(), getRawHeader(), getRawHeaders() are not implemented
* canSendHeaders(), sendHeaders() not implemented
* Named body segments are not implemented. Symfony2 uses multiple response instances for that.
  All methods referencing a code will only reference the default segment or throw an exception
  (append(), prepend(), insert()).
* All exception related code is not implemented.

### Zend_Controller_Action

* IMPORTANT: Make sure $this->_redirect and $this->_forward are always called with a leading "return" statement.
* $this->view only calls to a ParameterBag that temporarily holds all view parameters. Calling view helpers inside the controller won't work!

### Zend_Controller_Action_HelperBroker

The HelperBroker in this compatibility layer only implements the necessary ZF functionality.
You cannot extend it with your own helpers. Use the Dependency Injenction container in the Controller and request services to use:

class MyController extends ZendController
{
    public function indexAction()
    {
        $this->get('my.action.helper')->doAction();
    }
}

If someone cares please implement the helper broker as extensionable object, its
in `Whitewashing\Zend\Mvc1CompatBundle\Controller\Helpers\HelperBroker`. It should
use DIC tags to register helpers through an interface that has a getName() method
and the regular action helper stuff.

### List of ported Action Helpers

#### Url Action Helper

* $this->getHelper('url')->url($urlOptions, $name) will not allow to include 'controller', 'module' or 'action' parameters in $urlOptions as the original Zend router allows.
* $this->_helper->url($action, $ctrl, $module, $params) is an expensive method, iterating over the collection of all routes.
* The third parameter of the UrlHelper#url method is now $absolute = true/false, the original third and fourth parameter $reset/$encode have been dropped.