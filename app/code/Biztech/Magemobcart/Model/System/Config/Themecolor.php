<?php

namespace Biztech\Magemobcart\Model\System\Config;

class Themecolor
{
    public function toOptionArray()
    {
        return array(
                array('value'=>'240,94,47', 'label'=>__('Orange')),
                array('value'=>'37,188,200', 'label'=>__('Firozi')),
                array('value'=>'146,39,143', 'label'=>__('Purple')),
                array('value'=>'34,81,163', 'label'=>__('Dark Blue')),
                array('value'=>'3,169,244', 'label'=>__('Sky Blue')),
                array('value'=>'227,96,139', 'label'=>__('Pink')),
                array('value'=>'220,85,98', 'label'=>__('Redish')),
            );
    }
}
