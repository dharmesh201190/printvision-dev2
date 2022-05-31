<?php

namespace Biztech\Magemobcart\Model;

class Product
{
    public static function getOptionArray()
    {
        $result = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $productCollection->addAttributeToSelect('*');
        $productCollection->joinField(
            'stock_status',
            'cataloginventory_stock_status',
            'stock_status',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        )->addFieldToFilter('stock_status', array('eq' => \Magento\CatalogInventory\Model\Stock\Status::STATUS_IN_STOCK));
        $productCollection->load();
        foreach ($productCollection as $key => $product) {
            $productname = $product->getName();
            $result[$product['entity_id']] = $productname;
        }
        return $result;
    }
}
