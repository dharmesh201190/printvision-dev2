<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\Productdesigner\Model;

class Cart extends \Magento\Checkout\Model\Cart {

    public function addOrderItem($orderItem, $qtyFlag = null) {
        /* @var $orderItem \Magento\Sales\Model\Order\Item */
        if ($orderItem->getParentItem() === null) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {

                /**
                 * We need to reload product in this place, because products
                 * with the same id may have different sets of order attributes.
                 */
                $product = $this->productRepository->getById($orderItem->getProductId(), false, $storeId, true);
            } catch (NoSuchEntityException $e) {
                return $this;
            }
            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new \Magento\Framework\DataObject($info);
            if ($qtyFlag === null) {
                $info->setQty($orderItem->getQtyOrdered());
            } else {
                $info->setQty(1);
            }
            if($orderItem->getProductOptionByCode('additional_options')){
                $info->setAdditionalOptions($orderItem->getProductOptionByCode('additional_options'));
            }
            $product->setPrice($orderItem->getPrice());
            $product->setBasePrice($orderItem->getBasePrice());
            $product->setOriginalPrice($orderItem->getOriginalPrice());
            $product->setBaseOriginalPrice($orderItem->getBaseOriginalPrice());
            $product->setPriceInclTax($orderItem->getPriceInclTax());
            $product->setBasePriceInclTax($orderItem->getBasePriceInclTax());

            $product->setRowTotal($orderItem->getRowTotal());
            $product->setBaseRowTotal($orderItem->getBaseRowTotal());
            $product->setRowTotalInclTax($orderItem->getRowTotalInclTax());
            $product->setBaseRowTotalInclTax($orderItem->getBaseRowTotalInclTax());

            $this->addProduct($product, $info);
        }

        return $this;
    }

}
