<?php

namespace Biztech\Productdesigner\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class status implements ArrayInterface {

    const ENABLED = 1;
    const DISABLED = 2;

    /**
     * @return array
     */
    public function toOptionArray() {
        $options = [
            "" => __('Please Select'),
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];

        return $options;
    }

}
