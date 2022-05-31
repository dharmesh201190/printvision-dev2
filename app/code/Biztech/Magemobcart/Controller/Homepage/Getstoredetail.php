<?php

/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\Magemobcart\Controller\Homepage;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Getstoredetail extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_ENABLED = 'contact/contact/enabled';
    const XML_PATH_PRIVACY = 'magemobcart/magemobcart_general/privacypage_url';
    const XML_PATH_ABOUTUS = 'magemobcart/magemobcart_general/aboutuspage_url';
    const XML_PATH_FAQ = 'magemobcart/magemobcart_general/faq_url';
    const XML_PATH_GOOGLEAPI = 'magemobcart/googleanalytics/apinumber';
    const XML_PATH_SECURESITE = 'magemobcart/magemobcart_general/full_site_secure';
    const XML_PATH_HTTPSCONFIG = 'magemobcart/magemobcart_general/http_https_config';
    const XML_PATH_BARCODE = 'magemobcart/magemobcart_general/enable_barcode';
    const XML_PATH_COLOR = 'magemobcart/themeselection/primary_background';
    const XML_PATH_SECONDARYCOLOR = 'magemobcart/themeselection/secondary_background';
    const XML_PATH_LANGUAGECODE = 'general/locale/code';
    const XML_PATH_HELPTOLL = 'magemobcart/helpdesk/tollfreenumber';
    const XML_PATH_HELPEMAIL = 'magemobcart/helpdesk/helpemail';
    const XML_PATH_HELPADD = 'magemobcart/helpdesk/helpaddress';

    protected $jsonFactory;
    protected $storeManager;
    protected $cartHelper;
    protected $customerSession;
    protected $cartModel;
    protected $scopeConfig;
    protected $localeCurrency;
    protected $cmspageModel;
    protected $filterProvider;
    protected $_eventManager;
    protected $_objectFactory;


    /**
     * @param Context                                            $context
     * @param JsonFactory                                        $jsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Biztech\Magemobcart\Helper\Data                   $cartHelper
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Magento\Checkout\Model\Cart                       $cartModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Locale\CurrencyInterface        $localeCurrency
     * @param \Magento\Cms\Model\PageFactory                     $cmspageModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Cms\Model\PageFactory $cmspageModel,
        Http $request,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Framework\Event\Manager $manager
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_storeManager = $storeManager;
        $this->_cartHelper = $cartHelper;
        $this->_customerSession = $customerSession;
        $this->_cartModel = $cartModel;
        $this->_scopeConfig = $scopeConfig;
        $this->_localeCurrency = $localeCurrency;
        $this->_cmspageModel = $cmspageModel;
        $this->_filterProvider = $filterProvider;
        $this->_request = $request;
        $this->formKey = $formKey;
        $this->_eventManager = $manager;
        $this->_objectFactory = $objectFactory;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all store details of websites.
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        if ($this->_cartHelper->isEnable()) {
            if (!$this->_cartHelper->getHeaders()) {
                $errorResult = array('status' => false, 'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customerId = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customerId[0]);
                }
            }
            try {
                $isDefaultStoreId = $this->_storeManager->getStore()->getId();
                foreach ($this->_storeManager->getWebsites() as $website) {
                    $storeResponse['website'][] = array('website_id' => $website->getId(), 'website_name' => $website->getName());

                    foreach ($website->getGroups() as $group) {
                        $stores = $group->getStores();

                        foreach ($stores as $store) {
                            if ($store->getIsActive() == 1) {
                                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
                                $languageCode = $this->_scopeConfig->getValue(self::XML_PATH_LANGUAGECODE, $storeScope);
                                $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                                $currencySign = $this->_localeCurrency->getCurrency($currencyCode)->getSymbol();
                                if ($currencySign == "") {
                                    $currencySign = $currencyCode;
                                }
                                if ($store->getId() == $isDefaultStoreId) {
                                    $storeResponse['stores'][] = array('store_id' => $store->getId(), 'store_name' => $store->getName(), 'language_code' => $languageCode, 'is_default' => 1, 'currency_code' => $this->_storeManager->getStore()->getCurrentCurrencyCode(), 'currency_sign' => $currencySign, 'website_id' => $this->_storeManager->getStore($store->getStoreCode())->getWebsiteId());
                                } else {
                                    $currencySign = $this->_localeCurrency->getCurrency($this->_storeManager->getStore($store->getId())->getCurrentCurrencyCode())->getSymbol();
                                    $currencyCode = $this->_storeManager->getStore($store->getId())->getCurrentCurrencyCode();
                                    if ($currencySign == "") {
                                        $currencySign = $currencyCode;
                                    }

                                    $storeResponse['stores'][] = array('store_id' => $store->getId(), 'store_name' => $store->getName(), 'language_code' => $languageCode, 'currency_code' => $this->_storeManager->getStore($store->getId())->getCurrentCurrencyCode(), 'currency_sign' => $currencySign, 'website_id' => $this->_storeManager->getStore($store->getId())->getWebsiteId());
                                }
                            }
                        }
                    }
                }
                $cmspageModelObject = $this->_cmspageModel->create();

                // *********************** Privacy page content ****************************

                $privacypageContent = $cmspageModelObject->load($this->_scopeConfig->getValue(self::XML_PATH_PRIVACY, $storeScope))->getContent();
                $htmlPrivacyPage = $this->_filterProvider->getPageFilter()->filter($privacypageContent);
                $storeResponse['privacypage_url'] = $htmlPrivacyPage;

                // *********************** Aboutus page content ****************************

                $aboutuspageContent = $cmspageModelObject->load($this->_scopeConfig->getValue(self::XML_PATH_ABOUTUS, $storeScope))->getContent();
                $htmlAboutUsPage = $this->_filterProvider->getPageFilter()->filter($aboutuspageContent);
                $storeResponse['aboutuspage_url'] = $htmlAboutUsPage;

                // *********************** FAQ page content ****************************

                $aboutuspageContent = $cmspageModelObject->load($this->_scopeConfig->getValue(self::XML_PATH_FAQ, $storeScope))->getContent();
                $htmlFAQPage = $this->_filterProvider->getPageFilter()->filter($aboutuspageContent);
                $storeResponse['faq_url'] = $htmlFAQPage;

                // *********************** Help Desk ****************************
                $storeResponse['help_toll'] = $this->_scopeConfig->getValue(self::XML_PATH_HELPTOLL, $storeScope);

                $storeResponse['help_email'] = $this->_scopeConfig->getValue(self::XML_PATH_HELPEMAIL, $storeScope);

                $storeResponse['help_address'] = $this->_scopeConfig->getValue(self::XML_PATH_HELPADD, $storeScope);

                // *************************************************************************
                $storeResponse['byi_enabled'] = false;

                $enableContactUs = $this->_scopeConfig->getValue(self::XML_PATH_ENABLED, $storeScope);
                $storeResponse['enable_contactus'] = (bool) $enableContactUs;

                $themePrimarycolor = $this->_scopeConfig->getValue(self::XML_PATH_COLOR, $storeScope);
                $storeResponse['color'] = $themePrimarycolor;
                $themeSecondarycolor = $this->_scopeConfig->getValue(self::XML_PATH_SECONDARYCOLOR, $storeScope);
                $storeResponse['secondary_color'] = $themeSecondarycolor;

                $googleapi = $this->_scopeConfig->getValue(self::XML_PATH_GOOGLEAPI, $storeScope);
                $storeResponse['googleapi'] = $googleapi;

                $storeResponse['is_site_secure'] = $this->_scopeConfig->getValue(self::XML_PATH_SECURESITE, $storeScope);
                if ($storeResponse['is_site_secure'] == 0) {
                    $configValue = $this->_scopeConfig->getValue(self::XML_PATH_HTTPSCONFIG, $storeScope);
                    $modulesArray = array();
                    if (isset($configValue) && $configValue != '') {
                        $modulesArray = explode(',', $configValue);
                        $storeResponse['httpsCollection'] = $modulesArray;
                    } else {
                        $storeResponse['httpsCollection'] = $modulesArray;
                    }
                }
                $enableSku = $this->_scopeConfig->getValue(self::XML_PATH_BARCODE, $storeScope);
                $storeResponse['is_barcode'] = $enableSku;
                $storeResponse['magento_version'] = true;

                $customObject = $this->_objectFactory->create();
                $customObject->setStoreResponse($storeResponse);
                $this->_eventManager->dispatch('get_store_detail_before', ['storeResponse' => $customObject]);
                $storeResponse = $customObject->getStoreResponse();

            } catch (\Exception $e) {
                $storeResponse = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                );
            }
            $jsonResult->setData($storeResponse);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
