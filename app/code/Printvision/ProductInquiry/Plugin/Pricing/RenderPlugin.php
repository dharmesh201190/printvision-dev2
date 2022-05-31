<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Printvision\ProductInquiry\Plugin\Pricing;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

/**
 * Class RenderPlugin
 * @package Printvision\ProductInquiry\Plugin\Pricing
 */
class RenderPlugin extends \Magento\Catalog\Pricing\Render
{
    /**
     * RenderPlugin constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param \Printvision\ProductInquiry\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        \Printvision\ProductInquiry\Helper\Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $registry, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $product = $this->getProduct();
        $disclosePrice = $this->helperData->getDisclosePrice($product->getId());

        if ($disclosePrice) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
