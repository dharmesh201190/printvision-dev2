<?php

namespace Biztech\Productdesigner\Controller\Index;

class fetchLocale extends \Magento\Framework\App\Action\Action {

    protected $scopeConfig;
    protected $_storeManager;
    protected $_infoHelper;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Info $infoHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_infoHelper = $infoHelper;
        parent::__construct($context);
    }

    public function execute() {
        try {
            $storeid = $this->_storeManager->getStore()->getId();
            $stores = $this->_storeManager->getStores();
            foreach ($stores as $store) {
                $locale = explode("_", $this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode()));
                if ($storeid == $store->getId()) {
                    $active_locale[] = $locale[0];
                }
            }
            $response['active_locale'] = $active_locale;
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
