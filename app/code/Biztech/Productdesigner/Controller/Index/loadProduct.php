<?php

namespace Biztech\Productdesigner\Controller\Index;



class loadProduct extends \Biztech\Productdesigner\Controller\Index {

    /**
     * Pixels that are added while canvas creation
     */
    const CanvasRatio = 40;
    const Identifier = 'loadProduct';

    public function execute() {
        try {

            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = ($data['product_id']) ?: '';
            $designId = (isset($data['designId'])) ? base64_decode($data['designId']) : '';
            $selectedOptions = isset($data['selectedOptions']) ? $data['selectedOptions'] : '';
            $optionCacheKey = isset($data['optionCacheKey']) ? $data['optionCacheKey'] : '';
            $selectedOptionsString = ($selectedOptions) ? $this->_infoHelper->joinObject($selectedOptions) : '';
            /**
             * Load cache if exist
             */
            $cacheKey = self::Identifier . $productId . $selectedOptionsString . $designId . $optionCacheKey; /* Create key for unique cache identifier */
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                if ($productId) {
                    $response = $this->_infoHelper->getImageDimension($productId, $selectedOptions, $designId);
                } else {
                    $response = array();
                }
                $response['canvasRatio'] = self::CanvasRatio;
                $response['status'] = 'success';
                /**
                 * Set response in cache
                 */
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
