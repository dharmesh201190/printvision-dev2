<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\DesignTemplates\Controller\Adminhtml\Productdesigner;

class Index extends \Biztech\DesignTemplates\Controller\Adminhtml\Index {

    public function execute() {
        try {
            $productParams = $this->getRequest()->getParams();
            $productId = $productParams['id'];
            $product = $this->_productLoader->create()->load($productId);
            $layout = $this->layoutFactory->create()->createBlock('Biztech\DesignTemplates\Block\DesignTemplates');
            $store = $this->_storeManager->getStore();
            $storeId = $store->getId();
            $currencyCode = $store->getCurrentCurrencyCode();
            $baseUrl = $store->getBaseUrl();
            $isEnable = $this->_infoHelper->isEnable($storeId);
            $isPdEnable = $this->_infoHelper->isPdEnable($productParams['id'], $storeId);
            $pageTitle = $this->_pdHelper->getConfig('productdesigner/general/page_title', $storeId);
            $folderName = \Magento\Config\Model\Config\Backend\Image\Favicon::UPLOAD_DIR;
            $scopeConfig = $this->scopeConfig->getValue(
                'design/head/shortcut_icon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $path = $folderName . '/' . $scopeConfig;

            $mediaPath = $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            
            $pageLogo = $mediaPath . $path;
            $placeHolderImg= $this->_pdHelper->getConfig('productdesigner/general/placeholder', $storeId);
            $placeHolderUrl = $mediaPath . 'productdesigner/placeholder/'.$placeHolderImg;

            if(empty($placeHolderImg)){
                $placeHolderUrl = $mediaPath . 'productdesigner/placeholder.png';
            }


            $themeType = $this->_pdHelper->getConfig('productdesigner/themedesigner_general/theme_type', $storeId);
            /**
             * If found template id
             */
            if (isset($productParams['template'])) {
                $templateId = base64_decode($productParams['template']);
                    $super_attribute = $this->templateHelper->getSuperAttributesFromTemplate($templateId);
                    if ($super_attribute) {
                        $productParams['super_attribute'] = $super_attribute;
                    }
            }
            $productParams['store_id'] = base64_encode($storeId);
            $productParams['currency_code'] = base64_encode($currencyCode);
            $productParams['mage_base_url'] = base64_encode($baseUrl);
            $productParams['isEnable'] = $isEnable;
            $productParams['isPdEnable'] = $isPdEnable;
            $productParams['theme_type'] = $themeType;
            $productParams['placeHolderUrl'] = $placeHolderUrl;
            $result = $layout->setData(array("product_params" => $productParams, "page_title" => $pageTitle, "page_logo" => $pageLogo))->setTemplate('Biztech_DesignTemplates::productdesigner/productdesigner.phtml')->toHtml();
            $this->getResponse()->setBody($result);
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
