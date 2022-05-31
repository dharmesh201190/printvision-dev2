<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_JobBoard
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\JobBoard\Ui\Component\Listing\Columns\Options;

use Magento\Framework\Data\OptionSourceInterface;

class StatusType implements OptionSourceInterface
{
    /**
     * Get Enable Disable Option Array function
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' =>  __('Disable'),
                'value' => 0
            ],
            [
                'label' =>  __('Enable'),
                'value' => 1
            ]
        ];
        return $options;
    }
}
