<?php

namespace Biztech\CustomColorPicker\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {


    protected $_store;
    protected $_scopeConfig;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $store
    ) {
        
        $this->_store = $store;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    public function getConfig($data, $storeid = '') {
        if ($storeid) {
            $store = $this->_store->getStore($storeid);
            return $this->_scopeConfig->getValue($data, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
        } else {
            return $this->_scopeConfig->getValue($data, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
    }

}
