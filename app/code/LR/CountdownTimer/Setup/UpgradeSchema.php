<?php
namespace LR\CountdownTimer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $installer = $setup;
         if (version_compare($context->getVersion(), '1.0.1', '<')) {
            if (!$installer->tableExists('lr_delivery')) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable('lr_delivery'))
                    ->addColumn(
                        'delivery_id', 
                        Table::TYPE_INTEGER, 
                        null, 
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'Delivery Id'
                    )
                    ->addColumn(
                        'shipping_days',
                        Table::TYPE_INTEGER, 
                        null, 
                        ['nullable' => false, 'default' => '0'], 
                        'Shipping Days'
                    )
                    ->addColumn(
                        'shipping_label', 
                        Table::TYPE_TEXT, null, 
                        ['nullable' => false, 'default' => ''], 
                        'Shipping Label'
                    )->addColumn(
                        'status',
                        Table::TYPE_INTEGER,
                        1,
                        ['nullable' => false,'default' => 0],
                        'Status'
                    );
                $installer->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $tableName = $setup->getTable('lr_delivery');
            $connection = $setup->getConnection();
            $connection->addColumn(
                $tableName, 
                'sort_order', 
                [
                    'type' => Table::TYPE_INTEGER, 
                    'nullable' => false, 
                    'afters' => 'shipping_label', 
                    'length' => 6, 
                    'default' => 0, 
                    'comment' => 'Sort Order'
                ]
            );
        }

        $setup->endSetup();
    }
}
