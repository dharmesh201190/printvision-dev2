<?php


namespace Biztech\Productdesigner\Controller\Index;

class downloadConfiguration extends \Magento\Framework\App\Action\Action {

    protected $_storeManager;
    protected $_helper;
    protected $_infoHelper;

    const Identifier = 'downloadConfiguration';

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Helper\Info $infoHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_infoHelper = $infoHelper;
        parent::__construct($context);
    }

    public function execute() {
        try {
            $cacheKey = self::Identifier;
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $storeid = $this->_storeManager->getStore()->getId();
                $enableDownload = $this->_helper->getConfig('productdesigner/downloaddesign_general/enabled', $storeid);
                $response['enableDownload'] = $enableDownload;
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
