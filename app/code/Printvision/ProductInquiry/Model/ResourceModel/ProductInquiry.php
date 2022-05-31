<?php
namespace Printvision\ProductInquiry\Model\ResourceModel;

class ProductInquiry extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }
    
    protected function _construct()
    {
        $this->_init('printvision_product_inquiry', 'entity_id');
    }
}
