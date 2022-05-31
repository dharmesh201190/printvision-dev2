<?php
namespace Biztech\Productdesigner\Block;

use Magento\Config\Block\System\Config\Form\Field;

class Productdesigner extends Field {

    protected $_storeManager;
    protected $_productModel;

    public function __construct(
    \Magento\Catalog\Model\Product $productModel, \Magento\Backend\Block\Template\Context $context, array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_productModel = $productModel;

        parent::__construct($context, $data);
    }

    public function getBaseUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

}
