<?php
namespace Biztech\Productdesigner\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Enabledisable extends Field
{
    const XML_PATH_ACTIVATION = 'productdesigner/activation/key';
    protected $_scopeConfig;
    protected $_helper;
    protected $_resourceConfig;
    protected $_web;
    protected $_store;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Biztech\Productdesigner\Helper\Info $helper,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Store\Model\Website $web ,
        \Magento\Store\Model\Store $store ,
        
        array $data = []
    ){
        $this->_helper = $helper;
        
        $this->_web = $web;
        $this->_resourceConfig = $resourceConfig;
        $this->_store = $store;
        $this->storeManager = $context->getStoreManager();
        $this->_scopeConfig = $context->getScopeConfig(); 
        parent::__construct($context,$data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $websites   = $this->_helper->getAllWebsites();
        if (! empty($websites)) {
            $website_id = $this->getRequest()->getParam('website');        
            $website = $this->_web->load($website_id);
            if ($website && in_array($website->getWebsiteId(), $websites)) {
                $html = $element->getElementHtml();
            }
            elseif (! $website_id) {
                $html = $element->getElementHtml();
            }
            else {
                $html = '<strong class="required" style="color:red;">'.$this->__('Please buy additional domains').'</strong>';
            }
        }
        else{
            $websitecode =  $this->_request->getParam('website');
            $websiteId = $this->_store->load($websitecode)->getWebsiteId(); 
            $isenabled = $this->_storeManager->getWebsite($websiteId)->getConfig('productdesigner/activation/key');
            if($isenabled != null || $isenabled != ''){
               $html = sprintf('<strong class="required" style="color:red;">%s</strong>', __('Please select a website'));
               $modulestatus = $this->_resourceConfig;
               $modulestatus->saveConfig('productdesigner/activation/enabled', 0,'default',0);                   
           }else{
               $html = sprintf('<strong class="required" style="color:red;">%s</strong>', __('Please enter a valid key'));
           }
       }
       return $html;
   }
}
