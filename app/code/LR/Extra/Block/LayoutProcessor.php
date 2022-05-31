<?php
namespace LR\Extra\Block;

class LayoutProcessor
{
	public function afterProcess(
	    \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
	    array  $jsLayout) 
	{
			/* For shipping address */
			$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] ['validation']['required-entry'] = false;
			
	        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['label'] = __('County');

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['label'] = __('Postcode');

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street'] ['label'] = __('Delivery Address');

	        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['label'] = __('');

		    /* For billing address */
		    if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']
        	)) {
		    	foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as $key => $payment) 
		    	{
		    		/* City */
		    		if (isset($payment['children']['form-fields']['children']['city'])) {
						$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['city']['validation']['required-entry'] = false;
                	}

                	/* Region */
                	if (isset($payment['children']['form-fields']['children']['region_id'])) {
						$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['region_id']['label'] = __('County');
                	}

                	/* Postcode */
                	if (isset($payment['children']['form-fields']['children']['postcode'])) {
						$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['postcode']['label'] = __('Postcode');
                	}

                	/* Street */
                	if (isset($payment['children']['form-fields']['children']['street'])) {
	                	$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['street'] ['label'] = __('Delivery Address');
	                	$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['street']['children'][0]['label'] = __('');
					}
		    	}
        	}

		    unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['config']['tooltip']);

	        return $jsLayout;
	}
}
?>