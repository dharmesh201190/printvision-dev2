<?php

namespace Biztech\NameNumber\Observer;

use Magento\Framework\Event\ObserverInterface;

class getTabsData implements ObserverInterface {

    // 
    protected $_productLoader;

    public function __construct(
     \Magento\Catalog\Model\Product $_productLoader
 ) {
        $this->_productLoader = $_productLoader;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $tabsObject = $observer->getData('tabsObject');
        $tabsData =  $tabsObject->getTabsData();
        $productId = $tabsData['product_id'];
        $product = $this->_productLoader->load($productId);
        $product_type = $product->getTypeId();
        foreach($tabsData['allTabsData'] as $tabs){
            if($tabs['component'] == 'nameNumberComponent'){
                $nameNumberComponentId = $tabs['id'];
            }
        }
        if($product_type == 'simple'){
            foreach($tabsData['tempArray'] as $key=>$value){
                if($value == $nameNumberComponentId){
                    unset($tabsData['tempArray'][$key]);
                }
            }
            $tabsData['tempArray'] = array_values($tabsData['tempArray']);
        }
        if($product_type == 'configurable'){
            $nameNumberEnable = $product->getEnableNameNumber();
            if($nameNumberEnable != 1){
                foreach($tabsData['tempArray'] as $key=>$value){
                    if($value == $nameNumberComponentId){
                        unset($tabsData['tempArray'][$key]);
                    }
                }
                $tabsData['tempArray'] = array_values($tabsData['tempArray']);
            }
        }   
        $tabsObject->setTabsData($tabsData);
    }
}
