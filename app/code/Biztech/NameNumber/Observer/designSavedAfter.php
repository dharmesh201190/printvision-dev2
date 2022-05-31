<?php

namespace Biztech\NameNumber\Observer;

use Magento\Framework\Event\ObserverInterface;

class designSavedAfter implements ObserverInterface {

    // 
    protected $_designModel;

    public function __construct(
        \Biztech\Productdesigner\Model\Designs $design
    ) {
        $this->_designModel = $design;
    }

   
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $design_id = $observer->getData('design_id');
        $additional_options = $observer->getData('additional_options');
        if(isset($additional_options['nameNumber'])) {
            $additional_options = $additional_options['nameNumber'];
        }
        $additional_options = json_encode($additional_options);
        $savedDesignData = $this->_designModel->load($design_id);
        $savedDesignData->setNameNumberDetails($additional_options)->save();
    }
}
