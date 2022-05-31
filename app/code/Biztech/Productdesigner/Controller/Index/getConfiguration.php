<?php

namespace Biztech\Productdesigner\Controller\Index;

class getConfiguration extends \Magento\Framework\App\Action\Action {

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

    const Identifier = 'getConfiguration';

    public function execute() {
     try {
         $data = json_decode(file_get_contents('php://input'), TRUE);         
         $key = !empty($data['key']) ? $data['key'] : '';
         $cacheKey = self::Identifier.'-'.$key;
         $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                 $storeid = $this->_storeManager->getStore()->getId();
                 $response = array();
                 if(sizeof($data['configPaths']) > 0){
                     foreach ($data['configPaths'] as $key => $value) {
                         array_push($response, $this->_helper->getConfig($value, $storeid));
                     }
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
