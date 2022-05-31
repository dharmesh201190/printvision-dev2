<?php
namespace Printvision\ProductInquiry\Plugin\Pricing\Render;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Bundle\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\CustomOptionPrice;

class FinalPriceBoxPlugin extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * FinalPriceBoxPlugin constructor.
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param array $data
     * @param SalableResolverInterface|null $salableResolver
     * @param MinimalPriceCalculatorInterface|null $minimalPriceCalculator
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        \Printvision\ProductInquiry\Helper\Data $helperData,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        $this->helperData = $helperData;
        $this->productFactory = $productFactory;

        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $data,
            $salableResolver,
            $minimalPriceCalculator
        );
    }

    public function showRangePrice()
    {
        $disclosePrice = $this->helperData->getDisclosePrice($this->getSaleableItem()->getId());

        if ($disclosePrice) {
            /** @var FinalPrice $bundlePrice */
            $bundlePrice = $this->getPriceType(FinalPrice::PRICE_CODE);
            $showRange = $bundlePrice->getMinimalPrice() != $bundlePrice->getMaximalPrice();

            if (!$showRange) {
                //Check the custom options, if any
                /** @var \Magento\Catalog\Pricing\Price\CustomOptionPrice $customOptionPrice */
                $customOptionPrice = $this->getPriceType(CustomOptionPrice::PRICE_CODE);
                $showRange =
                    $customOptionPrice->getCustomOptionRange(true) != $customOptionPrice->getCustomOptionRange(false);
            }

            return $showRange;
        } else {
            return;
        }
    }

    /**
     * @param string $html
     * @return string
     */
    protected function wrapResult($html)
    {
        $disclosePrice = $this->helperData->getDisclosePrice($this->getSaleableItem()->getId());

        if ($disclosePrice) {
            return parent::wrapResult($html);
        } else {
            return;
        }
    }
}
