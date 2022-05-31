<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Block\Adminhtml;

use Biztech\Magemobcart\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;

class Enabledisable extends Field
{
    const XML_PATH_ACTIVATION = 'inventorysystem/activation/key';

    protected $_scopeConfig;
    protected $_helper;
    protected $_resourceConfig;
    protected $_web;
    protected $_store;
    protected $_request;

    /**
     * @param Context $context
     * @param Data $helper
     * @param Config $resourceConfig
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Website $web
     * @param Store $store
     * @param Http $Request
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        Config $resourceConfig,
        Website $web,
        Store $store,
        Http $Request,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_storeManager = $context->getStoreManager();
        $this->_web = $web;
        $this->_resourceConfig = $resourceConfig;
        $this->_store = $store;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_request = $Request;
        parent::__construct($context, $data);
    }

    /**
     * @param  AbstractElement $element
     * @return mixed
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $websites = $this->_helper->getAllWebsites();
        if (!empty($websites)) {
            $websiteId = $this->getRequest()->getParam('website');
            $website = $this->_web->load($websiteId, 'code');
            if ($website && in_array($website->getWebsiteId(), $websites)) {
                $html = $element->getElementHtml();
            } elseif (!$websiteId) {
                $html = $element->getElementHtml();
            } else {
                $html = sprintf('<strong class="required" style="color:red">%s</strong>', __('Please buy additional domains'));
            }
        } else {
            $websiteCode = $this->_request->getParam('website');
            $websiteId = $this->_store->load($websiteCode)->getWebsiteId();
            $isenabled = $this->_storeManager->getWebsite($websiteId)->getConfig('magemobcart/activation/key');
            if ($isenabled != null || $isenabled != '') {
                $html = sprintf('<strong class="required" style="color:red">%s</strong>', __('Please select a website'));
                $moduleStatus = $this->_resourceConfig;
                $moduleStatus->saveConfig('magemobcart/enableextension/enabled', 0, 'default', 0);
            } else {
                $html = sprintf('<strong class="required" style="color:red">%s</strong>', __('Please enter a valid keyas'));
            }
        }
        return $html;
    }
}
