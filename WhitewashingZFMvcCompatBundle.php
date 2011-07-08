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

namespace Whitewashing\ZFMvcCompatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WhitewashingZFMvcCompatBundle extends Bundle
{
    public function boot()
    {
        if ($this->container->has('whitewashing.zfmvcompat.db')) {
            \Zend_Db_Table::setDefaultAdapter($this->container->get('whitewashing.zfmvcompat.db'));
        }

        $refl = new \ReflectionClass('Zend_Session');
        $vars = array('_sessionStarted', '_readable', '_writable');
        foreach ($vars AS $var) {
            $property = $refl->getProperty($var);
            $property->setAccessible(true);
            $property->setValue(null, true);
        }
    }
}
