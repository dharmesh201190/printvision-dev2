<?php

namespace Biztech\Productdesigner\Controller\Index;

class Premium extends \Biztech\Productdesigner\Controller\Index {

    public function execute() {
        try {
            $productParams = $this->getRequest()->getParams();
            $layout = $this->layoutFactory->create()->createBlock('Biztech\Productdesigner\Block\Productdesigner');
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

            $mediaPath = $this->_storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            $path = $folderName . '/' . $scopeConfig;
            
            /* Start Added By A.S. */
            $integrationfavicon = $this->_pdHelper->getConfig('integration/other_settings/favicon', $storeId);
            $pageLogo = isset($integrationfavicon) ? $mediaPath . 'productdesigner/favicon/' . $integrationfavicon : $mediaPath . $path;
            /* End Added By A.S. */
            
            $placeHolderImg = $this->_pdHelper->getConfig('productdesigner/general/placeholder', $storeId);
            $placeHolderUrl = $mediaPath . 'productdesigner/placeholder/' . $placeHolderImg;

            if (empty($placeHolderImg)) {
                $placeHolderUrl = $mediaPath . 'productdesigner/placeholder.png';
            }
            $customObject = $this->_objectFactory->create();
            $customObject->setProductParams($productParams);
            $this->_eventManager->dispatch('addOtherParams', ['customObject' => $customObject]);
            $productParams = $customObject->getProductParams();

            $productParams['store_id'] = base64_encode($storeId);
            $productParams['currency_code'] = base64_encode($currencyCode);
            $productParams['mage_base_url'] = base64_encode($baseUrl);
            $productParams['isEnable'] = $isEnable;
            $productParams['isPdEnable'] = $isPdEnable;
            $productParams['placeHolderUrl'] = $placeHolderUrl;
            $result = $layout->setData(array("product_params" => $productParams, "page_title" => $pageTitle, "page_logo" => $pageLogo))->setTemplate('Biztech_Productdesigner::productdesigner/premium.phtml')->toHtml();
            $this->getResponse()->setBody($result);
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
