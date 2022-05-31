<?php
namespace Printvision\ProductInquiry\Model\ResourceModel\ProductInquiry;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'printvision_product_inquiry_post_collection';
    protected $_eventObject = 'product_inquiry_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Printvision\ProductInquiry\Model\ProductInquiry::class, \Printvision\ProductInquiry\Model\ResourceModel\ProductInquiry::class);
    }
}
