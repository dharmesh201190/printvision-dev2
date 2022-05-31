<?php


namespace Biztech\Productdesigner\Controller\Designs;

class LoadDesign extends \Biztech\Productdesigner\Controller\Designs {

    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $designId = ($data['designId']) ? base64_decode($data['designId']) : '';
            $productId = ($data['productId']) ? ($data['productId']) : '';
            $response = array();
            if ($designId) {
                /*
                 * Fetch design data from design Id
                 */
                $response = $this->_infoHelper->loadDesign($designId);
                $response['status'] = 'success';
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
