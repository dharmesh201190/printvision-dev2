<?php

namespace Biztech\TabsManagement\Model\Adminhtml\Config\Source;

class Maintab extends \Magento\Config\Block\System\Config\Form\Field {

    protected $tabsCollection;

    public function __construct(
        \Biztech\Productdesigner\Model\Mysql4\TabsData\CollectionFactory $tabsCollection
    ) {
        $this->tabsCollection = $tabsCollection;
    }

    public function toOptionArray() {
        $option_array = array();

        $model = $this->tabsCollection->create();
        $model->setOrder('sort_order','ASC');
        $tabs_collection = $model->getData();
      
        foreach ($tabs_collection as $key => $value) {
            $value['value'] = $value['id'];
            unset($value['id']);
            array_push($option_array, $value);
        }
        return $option_array;
    }

}
