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

class SalaryTypeOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get Salary Type Options Array
     *
     * @return Array $data
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => '1', 'label' => __('Per Annum')],
            ['value' => '2', 'label' => __('Per Month')]
        ];

        return $data;
    }
}
