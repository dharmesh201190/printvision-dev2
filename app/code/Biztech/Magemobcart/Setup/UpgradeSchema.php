<?php
/**
 *
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->tableExists('magemob_notification_history')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magemob_notification_history')
            )
                    ->addColumn(
                        'notification_history_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'notification_history_id'
                    )
                    ->addColumn(
                        'type',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'type'
                    )
                    ->addColumn(
                        'order_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'order_id'
                    )
                    ->addColumn(
                        'customer_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'customer_id'
                    )
                    ->addColumn(
                        'offer_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'offer_id'
                    )
                    ->addColumn(
                        'is_read',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'is_read'
                    )

                    ->addColumn(
                        'order_increment_id',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'order_increment_id'
                    )
                    ->addColumn(
                        'order_status',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'order_status'
                    )
                    ->addColumn(
                        'order_message',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'order_message'
                    )
                    ->addColumn(
                        'order_grandtotal',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'order_grandtotal'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        255,
                        ['nullable' => false],
                        'created_at'
                    );
            $installer->getConnection()->createTable($table);
        }
        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('magemobcart'),
                'is_showing',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Category Title Show'
                ]
            );
        }

        $setup->endSetup();
    }
}
