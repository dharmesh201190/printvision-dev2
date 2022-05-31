<?php

namespace Biztech\Productdesigner\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Rootcategory implements ArrayInterface {

    const NO = 0;
    const YES = 1;

    /**
     * @return array
     */
    public function toOptionArray() {
        $options = [
            self::YES => __('Yes'),
            self::NO => __('No')
        ];

        return $options;
    }

}
