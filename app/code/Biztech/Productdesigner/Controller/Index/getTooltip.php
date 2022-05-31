<?php

namespace Biztech\Productdesigner\Controller\Index;

class getTooltip extends \Magento\Framework\App\Action\Action {

    protected $_storeManager;
    protected $_helper;
    protected $_infoHelper;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Helper\Info $infoHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_infoHelper = $infoHelper;
        $this->_helper = $helper;
        parent::__construct($context);
    }
    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $isAdmin = isset($data['isAdmin']) ? $data['isAdmin'] : false;
            $cacheKey = 'getTooltip' . $isAdmin;
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $storeid = $this->_storeManager->getStore()->getId();
                $response = array();
                if ($isAdmin) {
                    $response['enable_tooltip'] = 0;
                } else {
                    $response['enable_tooltip'] = $this->_helper->getConfig('productdesigner/general/enable_tooltip', $storeid);
                }
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
