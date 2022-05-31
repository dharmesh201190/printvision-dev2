<?php
namespace Biztech\Productdesigner\Controller\ImageUpload;

class getImageConfiguration extends \Biztech\Productdesigner\Controller\Tabs {

    const Identifier = 'getImageConfiguration';

    public function execute() {
        try{
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = $data['product_id'];
            $cacheKey = self::Identifier; /* Create key for unique cache identifier */
            $response = $this->_infoHelper->loadCache($cacheKey);
            if(!$response){
                $response = array();
                $storeid = $this->_storeManager->getStore()->getId();
                $showInstruction = $this->_pdHelper->getConfig('productdesigner/upload_image_general/show_instruction', $storeid);
                $response['showInstruction'] = ($showInstruction == 1) ? true : false;
                $response['instructionText'] = htmlspecialchars_decode($this->_pdHelper->getConfig('productdesigner/upload_image_general/instruction_text', $storeid));
                $response['uploadLimitEnable'] = $this->_pdHelper->getConfig('productdesigner/upload_image_general/upload_limit', $storeid);
                $response['uploadImageLimit'] = $this->_pdHelper->getConfig('productdesigner/upload_image_general/upload_image_limit', $storeid);
                $response['limitAlert'] = htmlspecialchars_decode($this->_pdHelper->getConfig('productdesigner/upload_image_general/limit_alert', $storeid));
                $response['minImageSize'] = $this->_pdHelper->getConfig('productdesigner/upload_image_general/min_image_size', $storeid);
                $response['maxImageSize'] = $this->_pdHelper->getConfig('productdesigner/upload_image_general/max_image_size', $storeid);
                $response['confirmImageUpload'] = $this->_pdHelper->getConfig('productdesigner/upload_image_general/confirm_image_upload', $storeid);
                $response['confirmImageText'] = htmlspecialchars_decode($this->_pdHelper->getConfig('productdesigner/upload_image_general/confirm_image_text', $storeid));
                $this->_infoHelper->setCache($response,$cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
