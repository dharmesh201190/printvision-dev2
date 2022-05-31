<?php

namespace Biztech\Productdesigner\Model\Adminhtml\Config\Source;

class Colorpicker extends \Magento\Framework\Model\AbstractModel {

    protected $printableColorCollection;

    public function __construct(
     \Biztech\Productdesigner\Model\Mysql4\Printablecolor\CollectionFactory $printableColorCollection

    ) {
        $this->printableColorCollection = $printableColorCollection;
    }

    public function toOptionArray() {
        $model = $this->printableColorCollection->create();
        $collections = $model->getData();
        $color_array = array();
        foreach ($collections as $collection) {
            $color_array[] = array(
                'value' => $collection['printablecolor_id'],
                'label' => $collection['color_name']
            );
        }

        return $color_array;
    }

}
