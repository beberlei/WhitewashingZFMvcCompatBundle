<?php
/*
 * Whitewashing ZendMvc1CompatBundle
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Whitewashing\Zend\Mvc1CompatBundle\Controller\Helpers;

class Layout extends Helper
{
    private $layout = null;

    private $enabled = true;

    public function __construct($defaultLayoutResource)
    {
        $this->layout = $defaultLayoutResource;
    }

    public function disableLayout()
    {
        $this->enabled = false;
        return $this;
    }

    public function enableLayout()
    {
        $this->enabled = true;
        return $this;
    }

    public function setLayout($resource, $enable)
    {
        $this->layout = $resource;
        if ($enable) {
            $this->enabled = true;
        }
        return $this;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getName()
    {
        return 'layout';
    }

    public function direct()
    {
        return $this;
    }
}