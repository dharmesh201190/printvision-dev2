<?php

namespace Biztech\ThemeColors\Controller\Index;

class getPageConfiguration extends \Biztech\Productdesigner\Controller\Index\getPageConfiguration {

    public function execute() { 
        try {
            $cacheKey = 'getPageConfiguration';
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $storeid = $this->_storeManager->getStore()->getId();
                $integrationlogo = $this->_dataHelper->getConfig('integration/other_settings/logo_src', $storeid);
                $integrationlogoalt = $this->_dataHelper->getConfig('integration/other_settings/logo_alt', $storeid);
                $theme_type = $this->_scopeConfigInterface->getValue('productdesigner/themedesigner_general/theme_type');
                if ($theme_type == 'dark') {
                    $image = $this->_scopeConfigInterface->getValue('productdesigner/themedesigner_general/logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $logo = $path . 'productdesigner/darkthemelogo/' . $image;
                    if (($image != null) || ($image != '')) {
                        $response['logo_src'] = $logo;
                    } else {
                        $response['logo_src'] =isset($integrationlogo) ? $path . 'productdesigner/logo/'.$integrationlogo : $this->_logo->getLogoSrc();
                    }
                } else {
                    $response['logo_src'] =isset($integrationlogo) ? $path . 'productdesigner/logo/'.$integrationlogo : $this->_logo->getLogoSrc();
                }
                $response['logo_alt'] = $integrationlogoalt ?: $this->_logo->getLogoAlt();
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
