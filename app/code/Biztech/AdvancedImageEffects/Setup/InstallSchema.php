<?php

namespace Biztech\AdvancedImageEffects\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        
        $setup->getConnection()->addColumn($installer->getTable('productdesigner_imageeffects'), 'default_value', [
            'type' => Table::TYPE_TEXT,
            'nullable' => true,
            'default' => '',
            'comment' => 'Default Value'
            ]
        );
        
        $installer->endSetup();
    }
}
