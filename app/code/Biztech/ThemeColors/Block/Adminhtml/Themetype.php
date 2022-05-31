<?php

namespace Biztech\ThemeColors\Block\Adminhtml;

class Themetype extends \Magento\Config\Block\System\Config\Form\Field {

    public function toOptionArray() {
        return array(
            array('value' => 'light', 'label' => __('Light')),
            array('value' => 'dark', 'label' => __('Dark')),
        );
    }

}
