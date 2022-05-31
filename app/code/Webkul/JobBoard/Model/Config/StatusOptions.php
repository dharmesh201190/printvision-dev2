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
namespace Webkul\JObBoard\Model\Config;

class StatusOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get Status Option Array
     *
     * @return Array $data
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => '1', 'label' => __('Enabled')],
            ['value' => '0', 'label' => __('Disabled')]
        ];

        return $data;
    }
}
