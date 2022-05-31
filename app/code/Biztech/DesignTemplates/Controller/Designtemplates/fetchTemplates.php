<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\DesignTemplates\Controller\Designtemplates;

class fetchTemplates extends \Biztech\DesignTemplates\Controller\Designtemplates {

    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $params = json_decode(file_get_contents('php://input'), TRUE);
            $productDesignTemplatesCategory = [];
            if (isset($params['productId'])) {
                $product = $this->_productLoader->create()->load($params['productId']);
                $productDesignTemplatesCategory = explode(",", $product->getDesignTemplatesCategory());
            }
            $data = array(
                'templateCatId' => $params['templateCatId'],
                'searchText' => $params['searchText'],
                'page' => $params['page'],
                'limit' => $params['limit'],
                'productDesignTemplatesCategory' => $productDesignTemplatesCategory
            );
            $response = $this->templateHelper->fetchTemplates($data);
            $response['status'] = 'success';
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->templateHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
