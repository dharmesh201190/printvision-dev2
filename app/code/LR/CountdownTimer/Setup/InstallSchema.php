<?php

namespace LR\CountdownTimer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();

		/**
		 * Creating table lr_countdowntimer
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('lr_countdowntimer')
		)->addColumn(
			'countdowntimer_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Holiday Title'
		)->addColumn(
			'date',
			\Magento\Framework\DB\Ddl\Table::TYPE_DATE,
			null,
			['nullable' => false],
			'Date'
		)->addColumn(
			'created_at',
			\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
			null,
			['nullable' => false],
			'Created At'
		)->setComment(
            'LR CountdownTimer Table'
        );
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}