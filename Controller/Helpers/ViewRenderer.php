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

class ViewRenderer extends Helper
{
    private $noRender = false;
    static private $neverRender = false;

    public function getName()
    {
        return 'viewrenderer';
    }

    public function setNoRender($flag = true)
    {
        $this->noRender = (bool)$flag;
        return $this;
    }

    public function getNoRender()
    {
        return $this->noRender || self::$neverRender;
    }

    public function setNeverRender($flag = true)
    {
        self::$neverRender = (bool)$flag;
        return $this;
    }
}