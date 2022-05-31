<?php
/**
 *
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'catalog_product_entity_datetime'
         */
        
        if (!$installer->tableExists('magemob_bannerslider')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magemob_bannerslider')
            )
                    ->addColumn(
                        'bannerslider_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'bannerslider_id'
                    )
                    ->addColumn(
                        'title',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'title'
                    )
                    ->addColumn(
                        'filename',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'filename'
                    )
                    ->addColumn(
                        'filepath',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'filepath'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'status'
                    )
                    ->addColumn(
                        'type',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'type'
                    )
                    ->addColumn(
                        'url',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'url'
                    )
                    ->addColumn(
                        'product_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['product_id' => false],
                        'status'
                    )
                    ->addColumn(
                        'sort_order',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'sort_order'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'store_id'
                    )
                    ->addColumn(
                        'category',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'category'
                    );
            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('magemobcart')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magemobcart')
            )
                    ->addColumn(
                        'magemobcart_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'magemobcart_id'
                    )
                    ->addColumn(
                        'title',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'title'
                    )
                    ->addColumn(
                        'filename',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'filename'
                    )
                    ->addColumn(
                        'filepath',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'filepath'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'status'
                    )
                    ->addColumn(
                        'is_showing',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'is_showing'
                    )
                    ->addColumn(
                        'sort_order',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'sort_order'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'store_id'
                    )
                    ->addColumn(
                        'category',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'category'
                    );
            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('magemob_offerslider')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magemob_offerslider')
            )
                    ->addColumn(
                        'offerslider_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'offerslider_id'
                    )
                    ->addColumn(
                        'title',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'title'
                    )
                    ->addColumn(
                        'filename',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'filename'
                    )
                    ->addColumn(
                        'filepath',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'filepath'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'status'
                    )
                    ->addColumn(
                        'type',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'type'
                    )
                    ->addColumn(
                        'url',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'url'
                    )
                    ->addColumn(
                        'product_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['product_id' => false],
                        'status'
                    )
                    ->addColumn(
                        'sort_order',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'sort_order'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'store_id'
                    )
                    ->addColumn(
                        'category',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'category'
                    );
            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('magemob_devicedata')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magemob_devicedata')
            )
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'id'
                    )
                    ->addColumn(
                        'device_token',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'device_token'
                    )
                    ->addColumn(
                        'device_type',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'device_type'
                    )
                    ->addColumn(
                        'notify_enabled',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'notify_enabled'
                    )
                    ->addColumn(
                        'website_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['default' => false],
                        'website_id'
                    )
                    ->addColumn(
                        'customer_email',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'customer_email'
                    )
                    ->addColumn(
                        'password',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'password'
                    )
                    ->addColumn(
                        'is_logout',
                        Table::TYPE_INTEGER,
                        null,
                        ['default' => false],
                        'is_logout'
                    )
                    ->addColumn(
                        'customer_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['default' => false],
                        'customer_id'
                    )
                    ->addColumn(
                        'device_id',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'device_id'
                    )
                    ->addColumn(
                        'notification_id',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'notification_id'
                    )
                    ->addColumn(
                        'cron_status',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'cron_status'
                    );
            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('magemob_notification')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magemob_notification')
            )
                    ->addColumn(
                        'notification_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'notification_id'
                    )
                    ->addColumn(
                        'website_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['default' => false],
                        'website_id'
                    )
                    ->addColumn(
                        'title',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'title'
                    )
                    ->addColumn(
                        'url',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'url'
                    )
                    ->addColumn(
                        'message',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'message'
                    )
                    ->addColumn(
                        'os',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'os'
                    )
                    ->addColumn(
                        'is_sent',
                        Table::TYPE_INTEGER,
                        null,
                        ['default' => false],
                        'is_sent'
                    )
                    ->addColumn(
                        'type',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'type'
                    )
                    ->addColumn(
                        'category_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'category_id'
                    )
                    ->addColumn(
                        'product_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['default' => false],
                        'status'
                    )
                    ->addColumn(
                        'order_status',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'order_status'
                    )
                    ->addColumn(
                        'filename',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'filename'
                    )
                    ->addColumn(
                        'filepath',
                        Table::TYPE_TEXT,
                        255,
                        ['default' => null, 'nullable' => false],
                        'filepath'
                    )
                    ->addColumn(
                        'start_date',
                        Table::TYPE_TIMESTAMP,
                        255,
                        ['nullable' => false],
                        'start_date'
                    )
                    ->addColumn(
                        'end_date',
                        Table::TYPE_TIMESTAMP,
                        255,
                        ['nullable' => false],
                        'end_date'
                    );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
