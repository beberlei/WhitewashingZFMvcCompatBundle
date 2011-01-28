<?php

namespace Whitewashing\Zend\Mvc1CompatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WhitewashingZendMvc1CompatBundle extends Bundle
{
    

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }
}
