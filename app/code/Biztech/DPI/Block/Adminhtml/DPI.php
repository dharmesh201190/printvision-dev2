<?php

namespace Biztech\DPI\Block\Adminhtml;

class DPI extends \Magento\Config\Block\System\Config\Form\Field {

    public function toOptionArray() {
        return array(
            array('value' => '72', 'label' => __('72')),
            array('value' => '96', 'label' => __('96')),
            array('value' => '150', 'label' => __('150')),
            array('value' => '300', 'label' => __('300')),
            array('value' => '600', 'label' => __('600'))
        );
    }

}
