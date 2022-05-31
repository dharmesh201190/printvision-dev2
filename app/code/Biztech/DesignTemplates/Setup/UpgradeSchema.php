<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Setup;

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
		if (version_compare($context->getVersion(), '2.0.1', '<')) {
			$productDesignTemplateTable = $setup->getTable('productdesigner_designtemplates');
			if ($setup->tableExists($productDesignTemplateTable)) {
				$setup->getConnection()->addColumn($productDesignTemplateTable, 'status', [
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
						'length' => 6,
						'nullable' => false,
						'default' => 1,
						'comment' => 'Status',
					]
				);
			}
		}			
	}
}
