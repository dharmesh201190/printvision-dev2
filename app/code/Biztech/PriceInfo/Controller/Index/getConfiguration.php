<?php

namespace Biztech\PriceInfo\Controller\Index;

class getConfiguration extends \Magento\Framework\App\Action\Action {

    protected $_storeManager;
    protected $_helper;
    protected $_infoHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    const Identifier = 'getPriceConfiguration';

    public function execute() {
     try {
         $data = json_decode(file_get_contents('php://input'), TRUE);         
         $key = !empty($data['key']) ? $data['key'] : '';
         $cacheKey = self::Identifier.'-'.$key;
         $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $storeid = $this->_storeManager->getStore()->getId();
                $isDisplyInfo = $this->_helper->getConfig($value, $storeid);
                
                $this->_infoHelper->setCache($isDisplyInfo, $cacheKey);
             }
             $this->getResponse()->setBody(json_encode($response));
         } catch (\Exception $e) {
             $response = $this->_infoHelper->throwException($e, self::class);
             $this->getResponse()->setBody(json_encode($response));
         }
     }
 }
