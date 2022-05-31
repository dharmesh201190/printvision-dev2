<?php

namespace Biztech\Productdesigner\Controller\Index;

class getPageConfiguration extends \Magento\Framework\App\Action\Action {

    protected $_logo;
    protected $_storeManager;
    protected $_dataHelper;
    protected $_infoHelper;
    protected $_scopeConfigInterface;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $dataHelper, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Theme\Block\Html\Header\Logo $logo, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_storeManager = $storeManager;
        $this->_logo = $logo;
        $this->_infoHelper = $infoHelper;
        $this->_scopeConfigInterface = $scopeConfigInterface;
        parent::__construct($context);
    }

    public function execute() {
        try {
            $cacheKey = 'getPageConfiguration';
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $storeid = $this->_storeManager->getStore()->getId();
                $integrationlogo = $this->_dataHelper->getConfig('integration/other_settings/logo_src', $storeid);
                $integrationlogoalt = $this->_dataHelper->getConfig('integration/other_settings/logo_alt', $storeid);
                $response['logo_src'] =isset($integrationlogo) ? $path . 'productdesigner/logo/'.$integrationlogo : $this->_logo->getLogoSrc();
                $response['logo_alt'] = $integrationlogoalt ?: $this->_logo->getLogoAlt();
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
