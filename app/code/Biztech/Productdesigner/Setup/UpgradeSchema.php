<?php

namespace Biztech\Productdesigner\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	public function __construct(
		EavSetupFactory $eavSetupFactory
	) {
		$this->_eavSetupFactory = $eavSetupFactory;
	}

	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;
		$installer->startSetup();
		if (version_compare($context->getVersion(), '2.0.2', '<')) {
			if (!$installer->tableExists($installer->getTable('productdesigner_masking_category'))) {
				$table = $installer->getConnection()
				->newTable($installer->getTable('productdesigner_masking_category'))
				->addColumn(
					'category_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'category_id'
				)
				->addColumn(
					'masking_category_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'masking_category_title'
				);
				$installer->getConnection()->createTable($table);
			}		

			$productMaskingTable = $setup->getTable('productdesigner_masking');
			if ($setup->tableExists($productMaskingTable)) {
				$setup->getConnection()->addColumn($productMaskingTable, 'masking_category_id', [
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						'length' => 11,
						'nullable' => false,
						'default' => 0,
						'comment' => 'Masking Category Id',
					]
				);
			}


			$productImageEffectTable = $setup->getTable('productdesigner_imageeffects');
			if ($setup->tableExists($productImageEffectTable)) {
				$setup->getConnection()->addColumn($productImageEffectTable, 'is_filter', [
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						'length' => 11,
						'nullable' => false,
						'default' => 0,
						'comment' => 'Is filter',
					]
				);
			}
		}
		if (version_compare($context->getVersion(), '2.0.3', '<')) {
			$productDesignTable = $setup->getTable('productdesigner_designs');
			if ($setup->tableExists($productDesignTable)) {
				$setup->getConnection()->addColumn($productDesignTable, 'is_display', [
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						'length' => 11,
						'nullable' => false,
						'default' => 0,
						'comment' => 'Is This design Ordered',
					]
				);
			}
		}
	}
}
