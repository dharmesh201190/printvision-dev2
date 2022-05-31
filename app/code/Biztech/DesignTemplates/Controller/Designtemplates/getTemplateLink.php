<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\DesignTemplates\Controller\Designtemplates;

class getTemplateLink extends \Biztech\DesignTemplates\Controller\Designtemplates {

    public function execute() {
        try {
            $data = $this->getRequest()->getPostValue();

            $productId = $data['product'];
            $selectedOptions = $data['super_attribute'];
            $product = $this->_productLoader->create()->load($productId);
            $associatedProductId = $this->_infoHelper->fetchSelectedAssociatedProduct($selectedOptions, $product);
            $product_type = $product->getTypeId();
            $defaultAssociatedProduct = $defaultAssociatedProductTemplateId = "";
            if ($product_type == "configurable") {
                if (!$associatedProductId) {
                    $associatedProductId = $product->getDefaultAssociatedProduct();
                }
                $defaultAssociatedProduct = $this->_productLoader->create()->load($associatedProductId);
                if (!empty($defaultAssociatedProduct->getPreLoadedTemplate())) {
                    $defaultAssociatedProductTemplateId = base64_encode($defaultAssociatedProduct->getPreLoadedTemplate());
                }
            }
            $templteId = !empty($defaultAssociatedProductTemplateId) ? $defaultAssociatedProductTemplateId : base64_encode($product->getPreLoadedTemplate());
            $response['templateId'] = $templteId;
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->templateHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
