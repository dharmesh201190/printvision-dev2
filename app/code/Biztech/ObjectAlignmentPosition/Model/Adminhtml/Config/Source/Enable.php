<?php

namespace Biztech\ObjectAlignmentPosition\Model\Adminhtml\Config\Source;

class Enable implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        $options = array(
            array('value' => 0, 'label' => __('Disable')),
            array('value' => 1, 'label' => __('Enable'))
        );
        return $options;
    }

}
