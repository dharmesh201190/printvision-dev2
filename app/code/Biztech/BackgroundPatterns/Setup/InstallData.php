<?php

namespace Biztech\BackgroundPatterns\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallData implements InstallDataInterface {

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

        $tabs = [];
        $tabs[] = [
            'id' => 8,
            'label' => 'Background Patterns',
            'component' => 'backgroundPatternsComponent',
            'is_admin' => 1,
            'custom_label' => 'Background Patterns',
            'element_id' => '.byi-tooltip-backgroundPatternsComponent'
        ];

        if (count($tabs)) {
            $setup->getConnection()
            ->insertMultiple($setup->getTable('productdesigner_tabs'), $tabs);
        }
    }

}
