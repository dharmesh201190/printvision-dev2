<?php

namespace Biztech\Magemobcart\Model\System\Config;

class Httpsconfig
{
    public function toOptionArray()
    {
        $optionArray = array();
        $optionArray = array(
                array('value' => 'homepage', 'label' => __('Home Page')),
                array('value' => 'cart', 'label' => __('Cart')),
                array('value' => 'checkout', 'label' => __('Checkout')),
                array('value' => 'customer', 'label' => __('Customer')),
                array('value' => 'product', 'label' => __('Product'))
            );
        return $optionArray;
    }
}
