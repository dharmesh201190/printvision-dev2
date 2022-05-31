<?php

namespace Biztech\Watermark\Model\Config\Backend;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

class WatermarkType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $optionFactory;
    public function getAllOptions()
    {
        $this->_options=[
            ['label'=>'Image', 'value'=>'image'],
            ['label'=>'Text', 'value'=>'text']
        ];
        return $this->_options;
    }
}

