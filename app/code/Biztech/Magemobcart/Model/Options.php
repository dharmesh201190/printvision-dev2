<?php
 namespace Biztech\Magemobcart\Model;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options for Type
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
        ['value' =>  1, 'label' => __('Type 1')],
        ['value' =>  2, 'label' => __('Type 2')],
        ['value' =>  3, 'label' => __('Type 3')]
        ];
    }
}
