<?php

namespace Biztech\DesignTemplates\Model\System\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;


class Rootcategory extends AbstractSource implements SourceInterface, OptionSourceInterface {

    const NO = 0;
    const YES = 1;


    public static function getOptionArray()
    {
        return [self::YES => __('Yes'), self::NO => __('No')];
    }

    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

}
