<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Observer;

class CheckKey implements \Magento\Framework\Event\ObserverInterface
{
    const XML_PATH_ACTIVATIONKEY = 'magemobcart/activation/key';
    const XML_PATH_DATA = 'magemobcart/activation/data';
    const XML_GOOGLE_KEY = 'magemobcart/pushnotification/android_key';
    const XML_IOS_KEY = 'magemobcart/pushnotification/ios_key';

    protected $scopeConfig;
    protected $encryptor;
    protected $configFactory;
    protected $mobileassistantHelper;
    protected $request;
    protected $resourceConfig;
    protected $configModel;
    protected $configValueFactory;
    protected $zend;
    protected $cacheTypeList;
    protected $cacheFrontendPool;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Encryption\EncryptorInterface   $encryptor
     * @param \Magento\Config\Model\Config\Factory               $configFactory
     * @param \Biztech\Mobileassistant\Helper\Data               $mobileassistantHelper
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Zend\Json\Json                                    $zend
     * @param \Magento\Config\Model\ResourceModel\Config         $resourceConfig
     * @param \Magento\Framework\App\Config\ValueFactory         $configValueFactory
     * @param \Magento\Config\Model\Config                       $configModel
     * @param \Magento\Framework\App\Cache\TypeListInterface     $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool         $cacheFrontendPool
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Biztech\Magemobcart\Helper\Data $mobileassistantHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Zend\Json\Json $zend,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Config\Model\Config $configModel,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_encryptor = $encryptor;
        $this->_configFactory = $configFactory;
        $this->_mobileassistantHelper = $mobileassistantHelper;
        $this->_request = $request;
        $this->_zend = $zend;
        $this->_resourceConfig = $resourceConfig;
        $this->_configModel = $configModel;
        $this->_configValueFactory = $configValueFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * This function is used for check key is valid or not
     * @param  \Magento\Framework\Event\Observer $observer
     * @return Bool
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $k = $this->_scopeConfig->getValue(
            self::XML_PATH_ACTIVATIONKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $s = '';
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, sprintf('https://www.appjetty.com/extension/licence.php'));
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($k) . '&domains=' . urlencode(implode(',', $this->_mobileassistantHelper->getAllStoreDomains())) . '&sec=magento2-magemobcart');
        // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, sprintf('https://account.brushyourideas.com/extension/licence.php'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($k) . '&domains=' . urlencode(implode(',', $this->_mobileassistantHelper->getAllStoreDomains())) . '&sec=magento-brush-your-ideas');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $content = curl_exec($ch);
        $res1 = $this->_zend->decode($content);
        $res = (array) $res1;
        $moduleStatus = $this->_resourceConfig;

        if (empty($res)) {
            $moduleStatus->saveConfig('magemobcart/activation/key', "");
            $moduleStatus->saveConfig('magemobcart/magemobcart_general/active', 0);
            $data = $this->_scopeConfig('magemobcart/activation/data');
            $this->_resourceConfig->saveConfig('magemobcart/activation/data', $data, 'default', 0);
            $this->_resourceConfig->saveConfig('magemobcart/activation/websites', '', 'default', 0);
            return;
        }

        $data = '';
        $web = '';
        $en = '';

        if (isset($res['dom']) && intval($res['c']) > 0 && intval($res['suc']) == 1) {
            $data = $this->_encryptor->encrypt(base64_encode($this->_zend->encode($res1)));
            if (!$s) {
                $params = $this->_request->getParam('groups');
                if (isset($params['activation']['fields']['websites']['value'])) {
                    $s = $params['activation']['fields']['websites']['value'];
                }
            }

            $en = $res['suc'];
            if (isset($s) && $s != null) {
                $web = $this->_encryptor->encrypt($data . implode(',', $s) . $data);
            } else {
                $web = $this->_encryptor->encrypt($data . $data);
            }
        } else {
            $moduleStatus->saveConfig('magemobcart/activation/key', "", 'default', 0);
            $moduleStatus->saveConfig('magemobcart/magemobcart_general/active', 0, 'default', 0);
        }

        $this->_resourceConfig->saveConfig('magemobcart/activation/data', $data, 'default', 0);
        $this->_resourceConfig->saveConfig('magemobcart/activation/websites', $web, 'default', 0);
        $this->_resourceConfig->saveConfig('magemobcart/activation/en', $en, 'default', 0);
        $this->_resourceConfig->saveConfig('magemobcart/activation/installed', 1, 'default', 0);

        $types = array('config','full_page');
        
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }

        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    public function changePushNotificationSettings()
    {
        $googleKey = $this->_scopeConfig->getValue(self::XML_GOOGLE_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $iosKey = $this->_scopeConfig->getValue(self::XML_IOS_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($googleKey == "") {
            $this->_resourceConfig->saveConfig(self::XML_GOOGLE_KEY, "AAAAHY5cPFc:APA91bEEV3yT3BBh3MXpTzhKfJdpIgfr4giNHnNlRRtq7yQEP-wlrNrGhQO2RrJmRE9f7Yp8r79KY0ln-kH2W9pxTvXjGoFXLKpDAlyI9KZGQ6OeSRXwGOc0O4z8Z0zzH7DsdZBzeufT", 'default', 0);
        }
        if ($iosKey == "") {
            $this->_resourceConfig->saveConfig(self::XML_IOS_KEY, "AAAAHY5cPFc:APA91bEEV3yT3BBh3MXpTzhKfJdpIgfr4giNHnNlRRtq7yQEP-wlrNrGhQO2RrJmRE9f7Yp8r79KY0ln-kH2W9pxTvXjGoFXLKpDAlyI9KZGQ6OeSRXwGOc0O4z8Z0zzH7DsdZBzeufT", 'default', 0);
        }
        return true;
    }
}
