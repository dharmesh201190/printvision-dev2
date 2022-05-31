<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace SamSolutions\Artwork\Block\Order;

/**
 * Sales order view items block.
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Sales\Block\Order\Items
{
    public function isArtworkNeeded($item)
    {
        foreach ($item->getProductOptions()['additional_options'] as $option) {
            if ($option['value'] == 'Yes' || $option['value'] == '1') {
                return true;
            } else {
                return false;
            }
        }
    }
}
