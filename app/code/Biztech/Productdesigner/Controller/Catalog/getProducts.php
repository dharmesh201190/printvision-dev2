<?php

namespace Biztech\Productdesigner\Controller\Catalog;

class getProducts extends \Biztech\Productdesigner\Controller\Catalog {

    const Identifier = 'getProducts';

    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = $data['productId'];
            $search = $data['search'];
            $limit = $data['limit'];
            $categoryId = $data['categoryId'];
            $cacheKey = self::Identifier;
            $cacheKey = $cacheKey . $categoryId;
            $response = $this->_infoHelper->loadCache($cacheKey);
            $response = array();
            if (!$response) {
                $storeid = $this->_storeManager->getStore()->getId();
                $store = $this->_storeManager->getStore($storeid);
                $response['catalogProducts'] = $this->_infoHelper->fetchCatalogProducts($categoryId, $store, $search, $limit);
                $response['defaultCatId'] = $categoryId;
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
