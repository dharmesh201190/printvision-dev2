<?php
/**
 * Product Name: Mage2 Product Inquiry
 * Module Name: Mage2_Inquiry
 * Created By: Yogesh Shishangiya
 */

declare(strict_types=1);

namespace Mage2\Inquiry\Model\Inquiry\Source;

use Mage2\Inquiry\Model\Inquiry;
use Magento\Cms\Model\Block;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Mage2\Inquiry\Model\Inquiry\Source
 */
class Status implements OptionSourceInterface
{
    /**
     * @var Inquiry
     */
    protected $inquiry;

    /**
     * Constructor
     *
     * @param Inquiry $inquiry
     */
    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->inquiry->getAvailableStatuses();
        $options          = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
