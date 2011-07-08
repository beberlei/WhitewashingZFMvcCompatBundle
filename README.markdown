# Symfony2 Zend MVC 1.x Compatibility Bundle

Simplifies moving your Zend 1.x MVC apps to Symfony 2 if you follow the way I interpreted the Zend project guidelines closely enough :-)

## Overview

### What it can do:

* Has a base controller that mimics Zend_Controller_Action functionality
* Uses Zend_View as template engine and just replaces certain view helpers with the Symfony2 functionality.
* Ports most of the common action helpers or implements proxies that implement Symfony2 functionality.
* Adds a catch-all route mechanism for selected bundles.
* Import ZF Routing format files

### What it cannot do yet (Waiting for your pull requests)

* Support for custom Zend_View helpers (high priority)
* Expose Symfony View Globals such as the User through Zend_View
* Re-implement the Controller Plugin cycle (currently: use Symfony internals to port your plugins)
* All the inflection madness with dashes, lowercase, uppercase whatnot routing to controller/action naming. Currently only simple inflection is used.
* Context handling: The ContextSwitch and AjaxContext helpers are not ported yet.
* Have a console task to import a module from a ZF Project and do some work of the steps Installation automatically.

### What it will never do

* Make Zend Application code reusable (Use the dependency injection container)
* Handle calls to Zend_Controller_FrontController, you have to get rid of them.
* Make the ActionStack Helper work. This concept is flawed and should be replaced with calls to $this->action() in the view, which replaces it with Symfony internal functionality that is dispatching actions very fast.

### Example

In the ZFMvcCompatBundle\Resources\examples folder is an example bundle that implements the Guestbook tutorial as
a Symfony bundle using the compat layer. You can use it by adding "Application\ApplicationBundle()" as bundle to the Kernel,
configure the autoloader to use "Application" as namespace and "Application_" as directory. You can configure
the compat bundle with:

    whitewashing_zf_mvc_compat:
      default_layout_resource: "ApplicationBundle::layout.html.phtml"
      db_conn:
        adapter: pdo_mysql
        params:
          host: localhost
          username: root
          password:
          dbname: zfmvccompat

The database schema is:

    CREATE TABLE `guestbook` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `email` varchar(32) NOT NULL DEFAULT 'noemail@test.com',
     `comment` text,
     `created` datetime NOT NULL,
     PRIMARY KEY (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1

## Installation

1. Add the bundle to your AppKernel::registerBundles() method:

        return array(
            //..
            new Whitewashing\ZFMvcCompatBundle\WhitewashingZFMvcCompatBundle(),
        );

2. Add the Whitewashing namespace to your autolod.php.

3. Register Zend_View as template engine in your config.yml:

        framework:
            templating: { engines: ["twig", "phtml"] }

4. Enable the Compat Bundle in config.yml:

        whitewashing_zf_mvc_compat:
            default_layout_resource: "MyBundle::layout.html.phtml"

## Usage

It should be obvious that you won't be able to port your Zend Framework app to Symfony2 just by installing this bundle, manual labour will be necessary.
Each Zend Framework module will need to be ported to a Symfony2 Bundle.

1. Create a bundle for your module. The Bundle Name should be "ModuleName" + "Bundle". So in the case of a "blog" module you need to call your Bundle class "BlogBundle".
This can easily create clashes if you want to use a blog bundle built for Symfony in the future, but using this semantics you don't need to fix all your "_redirect", "_forward"
and redirector helper calls. If you do want to use a another bundle name then make sure that whenever you specify $module in the Zend API you need that to be $module . "Bundle".

2. Move all controllers into the $BundleRoot."/Controller/" directory and namespace the classes according to PSR-0. src/Appliction/BlogBundle/Controller/PostController.php
should become:

        namespace Application\BlogBundle\Controller;
        use Whitewashing\ZFMvcCompatBundle\Controller\ZendController;

        class PostController extends ZendController
        {
        }

IMPORTANT: Since your Controllers are now inside a namespace you have to either "use" import all classes or prefix them with \.

3. Move all your views into $BundleRoot."/Resources/views" and rename the "default" context html views into "viewName.html.phtml" instead of "viewName.phtml"

4. Move your layout into $BundleRoot."/Resources/views/layout.html.phtml"

Replace the call `$this->layout()->content` with `$this->content`

Different layout blocks are NOT supported. Use `$this->action()` for that.

5. View Layer: Replace the scriptname in calls to $this->partial() and $this->partialLoop() with the symfony resources, for example:

        <?php echo $this->partial("HelloBundle:Test:partial.html.phtml", array("name" => "Someone else!")); ?>

6. Routing

There is simple support for static and router routes in the compatibility layer through
``Whitewashing\ZFMvcCompatBundle\Router\Loader\ZFRouterLoader``. If you just use them you can
use xml, ini or php Zend_Config inputs by defining for example the Guestbook example:

    guestbookzf:
      type: zfmvc
      resource: "@WhitewashingZFMvcCompatBundle/Resources/examples/Application/Resources/config/routing.ini"

You should however convert all your Zend routes to Symfony routes, place them in a $BundleRoot."/Resources/routing.yml" and import
them in your app/config/routing.yml. Additionally Symfony has no "catch-all" routes by default, so you have to make use
of the catch all mechanism defined by the compat bundle:

        whitewashing_zf_mvc_compat:
            catchall_bundles: ["BlogBundle"]

When this mechanism is enabled you can request:

        http://appuri/{module}/{controller}/{action}

7. Security and ACLs

You probably implemented some kind of authentication, security and acl mechanism using controller plugins, Zend_Acl and
Zend_Auth. You have to use Zend_Acl inside a kernel event, preferably after routing took place to reimplement that logic.

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

* Url
* Redirector
* ViewRenderer
* Layout

#### Url Action Helper

* $this->getHelper('url')->url($urlOptions, $name) will not allow to include 'controller', 'module' or 'action' parameters in $urlOptions as the original Zend router allows.
* $this->_helper->url($action, $ctrl, $module, $params) is an expensive method, iterating over the collection of all routes.
* The third parameter of the UrlHelper#url method is now $absolute = true/false, the original third and fourth parameter $reset/$encode have been dropped.

#### Zend Layout

Parts of the API of `Zend_Layout` and the respective action helper has been ported, though it changed semantically.

In your config.yml when defining the `zendmvc1.compat:` section you have to specify a "default_layout_resource" parameter,
that takes of the form "BundleName::layoutFile.phtml" and resides in Bundle/Resources/views/layoutFile.phtml respectively.
The following very common API calls work:

    $this->_helper->layout()->disableLayout();
    $this->_helper->layout()->enableLayout();
    $this->_helper->layout()->setLayout("HelloBundle::layout.phtml", $enable)

As you can see, `setLayout` also expects a bundle resource, not a path anymore. You have to change all occurances
throughout your code, but I doubt that will be many.

#### View Renderer

Only the functions setNoRender() and setNeverRender() have been ported.