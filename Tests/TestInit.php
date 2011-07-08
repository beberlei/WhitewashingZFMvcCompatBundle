<?php

if (!isset($GLOBALS['SYMFONY2_SRC'])) {
    throw new \RuntimeException("SYMFONY2_SRC Global variable not specified. Use the shipped phpunit.dist.xml");
}
if (!isset($GLOBALS['ZF1_LIB'])) {
    throw new \RuntimeException("ZF1_LIB Global variable not specified. Use the shipped phpunit.dist.xml");
}

$symfonySrc = $GLOBALS['SYMFONY2_SRC'];

require_once $symfonySrc."/Symfony/Component/ClassLoader/UniversalClassLoader.php";

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'                        => $GLOBALS['SYMFONY2_SRC'],
));
$loader->registerPrefixes(array(
    'Zend_' => $GLOBALS['ZF1_LIB'],
));
$loader->register();

$files = array(
    "../View/CoreViewListener.php",
    "../View/ParameterBag.php",
    "../View/View1.php",
    "../View/ZendViewEngine.php",
    "../WhitewashingZFMvcCompatBundle.php",
    "../DependencyInjection/Mvc1CompatExtension.php",
    "../Controller/ZendController.php",
    "../Controller/ZendRequest.php",
    "../Controller/ZendResponse.php",
    "../Controller/RouteNameParser.php",
    "../Controller/CatchAllRequestListener.php",
    "../Controller/Helpers/Helper.php",
    "../Controller/Helpers/HelperBroker.php",
    "../Controller/Helpers/Layout.php",
    "../Controller/Helpers/ContextSwitch.php",
    "../Controller/Helpers/Redirector.php",
    "../Controller/Helpers/UrlHelper.php",
    "../Controller/Helpers/ViewRenderer.php",
);

foreach ($files AS $file) {
    require_once($file);
}