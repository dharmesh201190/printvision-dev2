<?php

namespace Biztech\NameNumber\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallData implements InstallDataInterface {

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

        $tabs = [];
        $tabs[] = [
            'id' => 6,
            'label' => 'Name Number',
            'component' => 'nameNumberComponent',
            'is_admin' => 0,
            'custom_label' => 'Name Number',
            'element_id' => '.byi-tooltip-nameNumberComponent'
        ];

        if (count($tabs)) {
            $setup->getConnection()
                    ->insertMultiple($setup->getTable('productdesigner_tabs'), $tabs);
        }
    }

}
