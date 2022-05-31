<?php

namespace Biztech\Productdesigner\Model\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;

use Magento\Framework\DB\Ddl\Table;

class Statusyesno extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $optionFactory;

    public function getAllOptions()
    {

        $this->_options=[ ['label'=>'Please Select', 'value'=>''],

            ['label'=>'Yes', 'value'=>'1'],

            ['label'=>'No', 'value'=>'0']
        ];
        return $this->_options;
    }
}

