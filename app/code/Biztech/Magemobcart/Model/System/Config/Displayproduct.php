<?php

namespace Biztech\Magemobcart\Model\System\Config;

class Displayproduct
{
    public function toOptionArray()
    {
        return array(
                array('value'=>1, 'label'=>__('Default')),
                array('value'=>2, 'label'=>__('Category wise')),
            );
    }
}
