<?php

namespace Biztech\Productdesigner\Controller\Index;

class preview extends \Biztech\Productdesigner\Controller\Index {

    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $hasDesign = (isset($data['hasObjects'])) ? $data['hasObjects'] : true;
            $dataUrls = ($data['dataUrl']) ?: '';
            $product_image_data = ($data['product_image_data']) ?: '';
            $product_image_path = $this->_infoHelper->convertRelToAbsPath($product_image_data['url']);
            $product_image_data['path'] = $product_image_path;
            $type = array('path' => 'preview');
            $response = $this->_infoHelper->generateImage($dataUrls, $product_image_data, $type, $hasDesign);
            $response['status'] = 'success';
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
