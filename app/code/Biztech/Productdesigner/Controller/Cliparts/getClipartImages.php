<?php

namespace Biztech\Productdesigner\Controller\Cliparts;


class getClipartImages extends \Biztech\Productdesigner\Controller\Cliparts {

    const Identifier = 'getClipartCategories';

    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = !empty($data['product_id']) ? $data['product_id'] : "";
            $clipartId = !empty($data['clipart_id']) ? $data['clipart_id'] : "";
            $page = $data['page'];
            $searchText = (!empty($data['search']) && $data['search'] != "") ? $data['search'] : "";
            $limit = $data['limit'];
            $cacheKey = self::Identifier . $productId . '-' . $clipartId . '-' . $page. '-' . $limit;
            $response = array();
                $clipartImages = $this->fetchClipartImages($clipartId, $limit, $searchText, $page);
                $response['clipart_images'] = $clipartImages['images'];
                $response['priceFormat'] = $clipartImages['priceFormat'];
                $response['loadMoreFlag'] = $clipartImages['loadMoreFlag'];
                $response['page'] = $clipartImages['page'];
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
