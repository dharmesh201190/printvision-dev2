<?php
namespace Magento\Tax\Model\Config;

/**
 * Interceptor class for @see \Magento\Tax\Model\Config
 */
class Interceptor extends \Magento\Tax\Model\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->___init();
        parent::__construct($scopeConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function priceIncludesTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'priceIncludesTax');
        if (!$pluginInfo) {
            return parent::priceIncludesTax($store);
        } else {
            return $this->___callPlugins('priceIncludesTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceIncludesTax($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setPriceIncludesTax');
        if (!$pluginInfo) {
            return parent::setPriceIncludesTax($value);
        } else {
            return $this->___callPlugins('setPriceIncludesTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyTaxAfterDiscount($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'applyTaxAfterDiscount');
        if (!$pluginInfo) {
            return parent::applyTaxAfterDiscount($store);
        } else {
            return $this->___callPlugins('applyTaxAfterDiscount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceDisplayType($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPriceDisplayType');
        if (!$pluginInfo) {
            return parent::getPriceDisplayType($store);
        } else {
            return $this->___callPlugins('getPriceDisplayType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function discountTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'discountTax');
        if (!$pluginInfo) {
            return parent::discountTax($store);
        } else {
            return $this->___callPlugins('discountTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculationSequence($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCalculationSequence');
        if (!$pluginInfo) {
            return parent::getCalculationSequence($store);
        } else {
            return $this->___callPlugins('getCalculationSequence', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setNeedUseShippingExcludeTax($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setNeedUseShippingExcludeTax');
        if (!$pluginInfo) {
            return parent::setNeedUseShippingExcludeTax($flag);
        } else {
            return $this->___callPlugins('setNeedUseShippingExcludeTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNeedUseShippingExcludeTax()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getNeedUseShippingExcludeTax');
        if (!$pluginInfo) {
            return parent::getNeedUseShippingExcludeTax();
        } else {
            return $this->___callPlugins('getNeedUseShippingExcludeTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlgorithm($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAlgorithm');
        if (!$pluginInfo) {
            return parent::getAlgorithm($store);
        } else {
            return $this->___callPlugins('getAlgorithm', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTaxClass($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getShippingTaxClass');
        if (!$pluginInfo) {
            return parent::getShippingTaxClass($store);
        } else {
            return $this->___callPlugins('getShippingTaxClass', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingPriceDisplayType($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getShippingPriceDisplayType');
        if (!$pluginInfo) {
            return parent::getShippingPriceDisplayType($store);
        } else {
            return $this->___callPlugins('getShippingPriceDisplayType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function shippingPriceIncludesTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'shippingPriceIncludesTax');
        if (!$pluginInfo) {
            return parent::shippingPriceIncludesTax($store);
        } else {
            return $this->___callPlugins('shippingPriceIncludesTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingPriceIncludeTax($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setShippingPriceIncludeTax');
        if (!$pluginInfo) {
            return parent::setShippingPriceIncludeTax($flag);
        } else {
            return $this->___callPlugins('setShippingPriceIncludeTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartPricesInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartPricesInclTax');
        if (!$pluginInfo) {
            return parent::displayCartPricesInclTax($store);
        } else {
            return $this->___callPlugins('displayCartPricesInclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartPricesExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartPricesExclTax');
        if (!$pluginInfo) {
            return parent::displayCartPricesExclTax($store);
        } else {
            return $this->___callPlugins('displayCartPricesExclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartPricesBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartPricesBoth');
        if (!$pluginInfo) {
            return parent::displayCartPricesBoth($store);
        } else {
            return $this->___callPlugins('displayCartPricesBoth', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartSubtotalInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartSubtotalInclTax');
        if (!$pluginInfo) {
            return parent::displayCartSubtotalInclTax($store);
        } else {
            return $this->___callPlugins('displayCartSubtotalInclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartSubtotalExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartSubtotalExclTax');
        if (!$pluginInfo) {
            return parent::displayCartSubtotalExclTax($store);
        } else {
            return $this->___callPlugins('displayCartSubtotalExclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartSubtotalBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartSubtotalBoth');
        if (!$pluginInfo) {
            return parent::displayCartSubtotalBoth($store);
        } else {
            return $this->___callPlugins('displayCartSubtotalBoth', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartShippingInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartShippingInclTax');
        if (!$pluginInfo) {
            return parent::displayCartShippingInclTax($store);
        } else {
            return $this->___callPlugins('displayCartShippingInclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartShippingExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartShippingExclTax');
        if (!$pluginInfo) {
            return parent::displayCartShippingExclTax($store);
        } else {
            return $this->___callPlugins('displayCartShippingExclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartShippingBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartShippingBoth');
        if (!$pluginInfo) {
            return parent::displayCartShippingBoth($store);
        } else {
            return $this->___callPlugins('displayCartShippingBoth', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartDiscountInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartDiscountInclTax');
        if (!$pluginInfo) {
            return parent::displayCartDiscountInclTax($store);
        } else {
            return $this->___callPlugins('displayCartDiscountInclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartDiscountExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartDiscountExclTax');
        if (!$pluginInfo) {
            return parent::displayCartDiscountExclTax($store);
        } else {
            return $this->___callPlugins('displayCartDiscountExclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartDiscountBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartDiscountBoth');
        if (!$pluginInfo) {
            return parent::displayCartDiscountBoth($store);
        } else {
            return $this->___callPlugins('displayCartDiscountBoth', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartTaxWithGrandTotal($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartTaxWithGrandTotal');
        if (!$pluginInfo) {
            return parent::displayCartTaxWithGrandTotal($store);
        } else {
            return $this->___callPlugins('displayCartTaxWithGrandTotal', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartFullSummary($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartFullSummary');
        if (!$pluginInfo) {
            return parent::displayCartFullSummary($store);
        } else {
            return $this->___callPlugins('displayCartFullSummary', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartZeroTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartZeroTax');
        if (!$pluginInfo) {
            return parent::displayCartZeroTax($store);
        } else {
            return $this->___callPlugins('displayCartZeroTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesPricesInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesPricesInclTax');
        if (!$pluginInfo) {
            return parent::displaySalesPricesInclTax($store);
        } else {
            return $this->___callPlugins('displaySalesPricesInclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesPricesExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesPricesExclTax');
        if (!$pluginInfo) {
            return parent::displaySalesPricesExclTax($store);
        } else {
            return $this->___callPlugins('displaySalesPricesExclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesPricesBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesPricesBoth');
        if (!$pluginInfo) {
            return parent::displaySalesPricesBoth($store);
        } else {
            return $this->___callPlugins('displaySalesPricesBoth', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesSubtotalInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesSubtotalInclTax');
        if (!$pluginInfo) {
            return parent::displaySalesSubtotalInclTax($store);
        } else {
            return $this->___callPlugins('displaySalesSubtotalInclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesSubtotalExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesSubtotalExclTax');
        if (!$pluginInfo) {
            return parent::displaySalesSubtotalExclTax($store);
        } else {
            return $this->___callPlugins('displaySalesSubtotalExclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesSubtotalBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesSubtotalBoth');
        if (!$pluginInfo) {
            return parent::displaySalesSubtotalBoth($store);
        } else {
            return $this->___callPlugins('displaySalesSubtotalBoth', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesShippingInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesShippingInclTax');
        if (!$pluginInfo) {
            return parent::displaySalesShippingInclTax($store);
        } else {
            return $this->___callPlugins('displaySalesShippingInclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesShippingExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesShippingExclTax');
        if (!$pluginInfo) {
            return parent::displaySalesShippingExclTax($store);
        } else {
            return $this->___callPlugins('displaySalesShippingExclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesShippingBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesShippingBoth');
        if (!$pluginInfo) {
            return parent::displaySalesShippingBoth($store);
        } else {
            return $this->___callPlugins('displaySalesShippingBoth', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesDiscountInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesDiscountInclTax');
        if (!$pluginInfo) {
            return parent::displaySalesDiscountInclTax($store);
        } else {
            return $this->___callPlugins('displaySalesDiscountInclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesDiscountExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesDiscountExclTax');
        if (!$pluginInfo) {
            return parent::displaySalesDiscountExclTax($store);
        } else {
            return $this->___callPlugins('displaySalesDiscountExclTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesDiscountBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesDiscountBoth');
        if (!$pluginInfo) {
            return parent::displaySalesDiscountBoth($store);
        } else {
            return $this->___callPlugins('displaySalesDiscountBoth', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesTaxWithGrandTotal($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesTaxWithGrandTotal');
        if (!$pluginInfo) {
            return parent::displaySalesTaxWithGrandTotal($store);
        } else {
            return $this->___callPlugins('displaySalesTaxWithGrandTotal', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesFullSummary($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesFullSummary');
        if (!$pluginInfo) {
            return parent::displaySalesFullSummary($store);
        } else {
            return $this->___callPlugins('displaySalesFullSummary', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesZeroTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesZeroTax');
        if (!$pluginInfo) {
            return parent::displaySalesZeroTax($store);
        } else {
            return $this->___callPlugins('displaySalesZeroTax', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function crossBorderTradeEnabled($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'crossBorderTradeEnabled');
        if (!$pluginInfo) {
            return parent::crossBorderTradeEnabled($store);
        } else {
            return $this->___callPlugins('crossBorderTradeEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isWrongApplyDiscountSettingIgnored($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWrongApplyDiscountSettingIgnored');
        if (!$pluginInfo) {
            return parent::isWrongApplyDiscountSettingIgnored($store);
        } else {
            return $this->___callPlugins('isWrongApplyDiscountSettingIgnored', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isWrongDisplaySettingsIgnored($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWrongDisplaySettingsIgnored');
        if (!$pluginInfo) {
            return parent::isWrongDisplaySettingsIgnored($store);
        } else {
            return $this->___callPlugins('isWrongDisplaySettingsIgnored', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isWrongDiscountSettingsIgnored($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWrongDiscountSettingsIgnored');
        if (!$pluginInfo) {
            return parent::isWrongDiscountSettingsIgnored($store);
        } else {
            return $this->___callPlugins('isWrongDiscountSettingsIgnored', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoUrl($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getInfoUrl');
        if (!$pluginInfo) {
            return parent::getInfoUrl($store);
        } else {
            return $this->___callPlugins('getInfoUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function needPriceConversion($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'needPriceConversion');
        if (!$pluginInfo) {
            return parent::needPriceConversion($store);
        } else {
            return $this->___callPlugins('needPriceConversion', func_get_args(), $pluginInfo);
        }
    }
}
