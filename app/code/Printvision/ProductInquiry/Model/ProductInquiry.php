<?php
namespace Printvision\ProductInquiry\Model;

class ProductInquiry extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'printvision_product_inquiry_post';
    protected $_cacheTag = 'printvision_product_inquiry_post';
    protected $_eventPrefix = 'printvision_product_inquiry_post';
    protected function _construct()
    {
        $this->_init(\Printvision\ProductInquiry\Model\ResourceModel\ProductInquiry::class);
    }
}
