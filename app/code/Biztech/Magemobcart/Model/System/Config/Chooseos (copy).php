<?php

namespace Biztech\Magemobcart\Model\System\Config;

class Chooseos
{
    public function toOptionArray()
    {
        return array(
                array('value'=>'all', 'label'=>__('All')),
                array('value'=>'android', 'label'=>__('Android')),
                array('value'=>'ios', 'label'=>__('IOS')),
            );
    }
}
