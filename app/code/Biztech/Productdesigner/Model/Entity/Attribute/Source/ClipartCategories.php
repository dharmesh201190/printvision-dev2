<?php

namespace Biztech\Productdesigner\Model\Entity\Attribute\Source;

class ClipartCategories extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    protected $request;
    protected $_options = [];
    protected $clipartCategoriesCollection;

    public function __construct(
    \Magento\Framework\App\Request\Http $request, \Biztech\Productdesigner\Model\Mysql4\Clipart\CollectionFactory $clipartCategoriesCollection
    ) {
        $this->request = $request;
        $this->clipartCategoriesCollection = $clipartCategoriesCollection;
    }
     public function getAllOptions() {
        $model = $this->clipartCategoriesCollection->create();
        $collection = $model->getData();
        $template_array = array();
        $template_array [] = array(
                    'label' => 'Please select clipart category',
                    'value' => ''
                );
        foreach ($collection as $designtemplatescategry) {
            if($designtemplatescategry['is_root_category'] == '1' && $designtemplatescategry['status'] == 1){
                $label = $designtemplatescategry['clipart_title'];
                $template_array[] = array(
                    'label' => $label,
                    'value' => $designtemplatescategry['clipart_id']
                );
            }
        }
        return $template_array;
    }

}
