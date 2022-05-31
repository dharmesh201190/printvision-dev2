<?php

namespace Biztech\Productdesigner\Observer;

use Magento\Framework\Event\ObserverInterface;

class checkKey implements ObserverInterface {

    const XML_PATH_ACTIVATIONKEY = 'productdesigner/activation/key';

    protected $_scopeConfig;
    protected $_storeManager;
    protected $encryptor;
    protected $_helper;
    protected $_request;
    protected $_resourceConfig;
    protected $_zend;
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Encryption\EncryptorInterface $encryptor, \Biztech\Productdesigner\Helper\Data $helper, \Magento\Framework\App\RequestInterface $request, \Zend\Json\Json $zend, \Magento\Config\Model\ResourceModel\Config $resourceConfig, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool, \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->encryptor = $encryptor;
        $this->_helper = $helper;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_request = $request;
        $this->_zend = $zend;
        $this->_resourceConfig = $resourceConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        if ($observer->getData()['website'] != '' || $observer->getData()['store'] != '') {
            return;
        }
        $k = $this->_scopeConfig->getValue(self::XML_PATH_ACTIVATIONKEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $s = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('https://account.brushyourideas.com/extension/licence.php'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($k) . '&domains=' . urlencode(implode(',', $this->_helper->getAllStoreDomains())) . '&sec=magento-brush-your-ideas');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $content = curl_exec($ch);
        $res1 = json_decode($content);
        $res = (array) $res1;
        if (empty($res)) {
            $this->_resourceConfig->saveConfig('productdesigner/activation/key', '', 'default', 0);
            $this->_resourceConfig->saveConfig('productdesigner/activation/enabled', '', 'default', 0);
            $data = $this->_scopeConfig->getValue('productdesigner/activation/data', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->_resourceConfig->saveConfig('productdesigner/activation/data', $data, 'default', 0);
            $this->_resourceConfig->saveConfig('productdesigner/activation/websites', '', 'default', 0);
            return;
        }
        $data = '';
        $web = '';
        $en = '';
        if (isset($res['dom']) && intval($res['c']) > 0 && intval($res['suc']) == 1) {
            $data = $this->encryptor->encrypt(base64_encode($this->_zend->encode($res1)));
            if (!$s) {
                $params = $this->_request->getParam('groups');
                if (isset($params['activation']['fields']['websites']['value'])) {
                    $s = $params['activation']['fields']['websites']['value'];
                }
            }
            $en = $res['suc'];
            if (isset($s) && $s != null) {
                $web = $this->encryptor->encrypt($data . implode(',', $s) . $data);
            } else {
                $web = $this->encryptor->encrypt($data . $data);
            }
        } else {
            $this->_resourceConfig->saveConfig('productdesigner/activation/key', "", 'default', 0);
            $this->_resourceConfig->saveConfig('productdesigner/activation/enabled', 0, 'default', 0);
        }
        $this->_resourceConfig->saveConfig('productdesigner/activation/data', $data, 'default', 0);
        $this->_resourceConfig->saveConfig('productdesigner/activation/websites', $web, 'default', 0);
        $this->_resourceConfig->saveConfig('productdesigner/activation/en', $en, 'default', 0);
        $this->_resourceConfig->saveConfig('productdesigner/activation/installed', 1, 'default', 0);

        /*
         */
        $types = array('config');
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

}
