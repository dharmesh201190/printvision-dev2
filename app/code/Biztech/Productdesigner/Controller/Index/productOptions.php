<?php


namespace Biztech\Productdesigner\Controller\Index;

class productOptions extends \Biztech\Productdesigner\Controller\Index {

    const Identifier = 'productOptions';

    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = ($data['product_id']) ?: '';
            $designId = (isset($data['designId'])) ? base64_decode($data['designId']) : '';
            $selectedOptions = isset($data['selectedOptions']) ? $data['selectedOptions'] : '';
            $selectedOptionsString = ($selectedOptions) ? $this->_infoHelper->joinObject($selectedOptions) : '';
            /**
             * Load cache if exist
             */
            $cacheKey = self::Identifier . $productId . $selectedOptionsString; /* Create key for unique cache identifier */
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                if ($productId) {
                    $response = $this->_infoHelper->getProductOptions($productId, $selectedOptions, $designId);
                } else {
                    $response = array();
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
