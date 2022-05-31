<?php

namespace Biztech\NameNumber\Observer;

use Magento\Framework\Event\ObserverInterface;

class getSubTabsData implements ObserverInterface {

    // 
    protected $_productLoader;

    public function __construct(
    \Magento\Catalog\Model\Product $_productLoader
    ) {
        $this->_productLoader = $_productLoader;
    }

   
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $subTabsObject = $observer->getData('subTabsObject');
        $subTabsData = $subTabsObject->getSubTabsObjectData();
        $productId = $subTabsData['product_id'];
        $product = $this->_productLoader->load($productId);
        $product_type = $product->getTypeId();
        foreach ($subTabsData['allTabsData'] as $tabs) {
            if ($tabs['component'] == 'nameNumberComponent') {
                $nameNumberComponentId = $tabs['id'];
            }
        }
        if ($product_type == 'simple') {
            foreach ($subTabsData['subtabs'] as $key => $value) {
                foreach ($value as $key1 => $data) {
                    if ($data == $nameNumberComponentId) {
                        unset($subTabsData['subtabs'][$key][$key1]);
                    }
                }
                $subTabsData['subtabs'][$key] = array_values($subTabsData['subtabs'][$key]);
            }
        }
        if ($product_type == 'configurable') {
            $nameNumberEnable = $product->getEnableNameNumber();
            if ($nameNumberEnable != 1) {
                foreach ($subTabsData['subtabs'] as $key => $value) {
                    foreach ($value as $key1 => $data) {
                        if ($data == $nameNumberComponentId) {
                            unset($subTabsData['subtabs'][$key][$key1]);
                        }
                    }
                    $subTabsData['subtabs'][$key] = array_values($subTabsData['subtabs'][$key]);
                }
            }
        }
        $subTabsObject->setActualSubTabsObjectData($subTabsData['subtabs']);
    }

}
