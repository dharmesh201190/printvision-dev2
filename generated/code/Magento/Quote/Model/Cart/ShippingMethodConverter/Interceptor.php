<?php
namespace Magento\Quote\Model\Cart\ShippingMethodConverter;

/**
 * Interceptor class for @see \Magento\Quote\Model\Cart\ShippingMethodConverter
 */
class Interceptor extends \Magento\Quote\Model\Cart\ShippingMethodConverter implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Quote\Api\Data\ShippingMethodInterfaceFactory $shippingMethodDataFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Tax\Helper\Data $taxHelper)
    {
        $this->___init();
        parent::__construct($shippingMethodDataFactory, $storeManager, $taxHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function modelToDataObject($rateModel, $quoteCurrencyCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'modelToDataObject');
        if (!$pluginInfo) {
            return parent::modelToDataObject($rateModel, $quoteCurrencyCode);
        } else {
            return $this->___callPlugins('modelToDataObject', func_get_args(), $pluginInfo);
        }
    }
}
