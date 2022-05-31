<?php

namespace Biztech\Productdesigner\Model\Adminhtml\Config\Source;

class Clipartcategories extends \Magento\Framework\Model\AbstractModel {

    protected $clipartCategoriesCollection;

    public function __construct(
      \Biztech\Productdesigner\Model\Mysql4\Clipart\CollectionFactory $clipartCategoriesCollection
    ) {
        $this->clipartCategoriesCollection = $clipartCategoriesCollection;
    }

    public function toOptionArray() {
        $model = $this->clipartCategoriesCollection->create();
        $collection = $model->getData();
        $template_array = array();
        foreach ($collection as $designtemplatescategry) {
           
            if($designtemplatescategry['is_root_category'] == '1' && $designtemplatescategry['status'] == 1){
                $label = $designtemplatescategry['clipart_title'];
                $template_array[] = array(
                    'label' => $label,
                    'value' => $designtemplatescategry['clipart_id'],
                    'status' => $designtemplatescategry['status']
                );
            }
        }
        return $template_array;
    }

}
