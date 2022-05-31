<?php

namespace Biztech\Namenumber\Block;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Namenumber extends Field {

    protected $_storeManager;
    protected $_designFactory;
    public $_designImagesCollection;
    public $_resourceConnection;
    public $_sideCollection;
    public $_scopeConfigInterface;
    public $_storeManagerInterface;
   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Biztech\Productdesigner\Model\DesignsFactory $designFactory,
        \Biztech\Productdesigner\Model\Mysql4\Designimages\CollectionFactory $designImagesCollection,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Biztech\Productdesigner\Model\Mysql4\Side\CollectionFactory $sideCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_designFactory = $designFactory;
        $this->_designImagesCollection = $designImagesCollection;
        $this->_resourceConnection = $resourceConnection;
        $this->_sideCollection = $sideCollection;
        $this->_scopeConfigInterface = $scopeConfigInterface;
        $this->_storeManagerInterface = $storeManagerInterface;
        parent::__construct($context, $data);
    }

    public function getDesignCollection($designId){
        if(isset($designId) && $designId != ''){
            $data = $this->_designFactory->create()->load($designId)->getData();
            if(isset($data)){
                return $data;
            }
        }
    }

    public function getBaseUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

}
