<?php
namespace Biztech\Productdesigner\Controller\Index;

use Biztech\Productdesigner\Model\ImageeffectsFactory;

class getImageEffects extends \Magento\Framework\App\Action\Action {

    protected $_effects;
    protected $_infoHelper;
    protected $_storeManager;
    protected $_pdHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Info $infoHelper, ImageeffectsFactory $effects, \Biztech\Productdesigner\Helper\Data $pdHelper
    ) {
        $this->_infoHelper = $infoHelper;
        $this->_effects = $effects;
        $this->_storeManager = $storeManager;
        $this->_pdHelper = $pdHelper;
        parent::__construct($context);
    }

    const Identifier = 'getImageEffects';

    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $cacheKey = self::Identifier;
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $response = array();
                $effects_collection = $this->_effects->create()->getCollection()->addFieldToFilter('status', '1')->getData();
            
                $path = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                );
                $storeId = $this->_storeManager->getStore()->getId();
                $placeHolderImg= $this->_pdHelper->getConfig('productdesigner/general/placeholder', $storeId);
                $placeHolderUrl = $path . 'productdesigner/placeholder/'.$placeHolderImg;

                if(empty($placeHolderImg)){
                    $placeHolderUrl = $path . 'productdesigner/placeholder.png';
                }
                
                if (sizeof($effects_collection) > 0) {
                    foreach ($effects_collection as $key => $value) {
                        if(!empty($value['effect_image'])){
                            $effects_collection[$key]['effect_image'] = $path. $value['effect_image'];
                        }else{
                            $effects_collection[$key]['effect_image'] = $placeHolderUrl;
                        }
                        
                        if(!empty($value['default_value'])){
                            $slider_data = json_decode($value['default_value'],true);
                            $effects_collection[$key]['min'] = $slider_data['min'];
                            $effects_collection[$key]['max'] = $slider_data['max'];
                            $effects_collection[$key]['step'] = $slider_data['step'];
                            $effects_collection[$key]['slider_value'] = $slider_data['value'];
                        }
                    }
                    $response['effects'] = $effects_collection;
                }else{
                   $response['effects'] = "";
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
