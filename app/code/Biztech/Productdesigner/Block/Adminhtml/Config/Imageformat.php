<?php

namespace Biztech\Productdesigner\Block\Adminhtml\Config;

class Imageformat extends \Magento\Config\Block\System\Config\Form\Field {

    public function toOptionArray() {
        return array(
            array('value' => 'png', 'label' => __('PNG')),
            array('value' => 'jpg', 'label' => __('JPG')),
        );
    }

}
