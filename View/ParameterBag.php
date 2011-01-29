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

namespace Whitewashing\Zend\Mvc1CompatBundle\View;

class ParameterBag
{
    private $params = array();

    public function assign($spec, $value = null)
    {
        if (is_array($spec) && !$value) {
            foreach ($spec as $k => $v) {
                $this->assign($k, $v);
            }
        } else {
            $this->params[$spec] = $value;
        }
    }

    public function clearVars()
    {
        $this->params = array();
        return $this;
    }

    public function __isset($offset)
    {
        return isset($this->params[$offset]);
    }

    public function __get($offset)
    {
        return $this->params[$offset];
    }

    public function __set($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    public function __unset($offset)
    {
        unset($this->params[$offset]);
    }

    public function allVars()
    {
        return $this->params;
    }
}