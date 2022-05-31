<?php

namespace StripeIntegration\Payments\Plugin\Tax;

class Config
{
    public function __construct(
        \StripeIntegration\Payments\Helper\Generic $helper
    )
    {
        $this->helper = $helper;
    }
    public function aroundGetAlgorithm(
        $subject,
        \Closure $proceed,
        $storeId
    ) {
        $algorithm = $proceed($storeId);

        // If the order includes subscriptions, we need to overwrite the tax calculation algorithm to Unit,
        // because tax is calculated on a per-subscription basis
        if ($algorithm != \Magento\Tax\Model\Calculation::CALC_UNIT_BASE && $this->helper->hasSubscriptions())
            return \Magento\Tax\Model\Calculation::CALC_UNIT_BASE;

        return $algorithm;
    }
}
