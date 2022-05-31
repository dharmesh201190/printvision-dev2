<?php

namespace Biztech\BackgroundPatterns\Observer;

class AddFilterClipart implements \Magento\Framework\Event\ObserverInterface {

    protected $_eventManager;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\Event\Manager $manager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_eventManager = $manager;
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $clipartCategoriesCollection = $observer->getData('clipartCategoriesCollection');
        $type = $observer->getData('type');
        if($type == 'clipart'){
            $clipartCategoriesCollection->addFieldToFilter("is_pattern", array("eq" => 0));
        }else if($type == 'pattern'){
            $clipartCategoriesCollection->addFieldToFilter("is_pattern", array("eq" => 1));
        }
    }

}
