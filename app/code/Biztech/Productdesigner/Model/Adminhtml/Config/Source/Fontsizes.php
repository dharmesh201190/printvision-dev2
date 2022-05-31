<?php

namespace Biztech\Productdesigner\Model\Adminhtml\Config\Source;

class Fontsizes extends \Magento\Config\Block\System\Config\Form\Field {

    public function toOptionArray() {
        for ($i = 12; $i <= 60; $i = $i + 2) {
            $option_array[] = array(
                'value' => $i,
                'label' => $i
            );
        }
        return $option_array;
    }

}
