<?php
namespace Printvision\ProductInquiry\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
 
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
 
        $installer->startSetup();
 
        $table = $installer->getConnection()
            ->newTable($installer->getTable('printvision_product_inquiry'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Primary Id'
            )
              ->addColumn(
                  'name',
                  Table::TYPE_TEXT,
                  null,
                  ['nullable' => false],
                  'Name'
              )
            ->addColumn(
                'phone',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Phone'
            )
              ->addColumn(
                  'email',
                  Table::TYPE_TEXT,
                  null,
                  ['nullable' => false],
                  'Email'
              )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Description'
            )
            ->addColumn(
                'sku',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'SKU'
            )
            ->addColumn(
                'company',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Company'
            )
            ->addColumn(
                'producttype',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Producttype'
            )
            ->addColumn(
                'quantityrequired',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Quantityrequired'
            )
            ->addColumn(
                'expectedarrivedate',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Expectedarrivedate'
            )
            ->addColumn(
                'callback',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Callback'
            )

            ->addColumn(
                'datetime',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                ],
                'Created At'
            )
            ->setComment('Printvision product inquiry table');

        $installer->getConnection()->createTable($table);

        $installer->getConnection()->addIndex(
            $installer->getTable('printvision_product_inquiry'),
            $setup->getIdxName(
                $installer->getTable('printvision_product_inquiry'),
                ['email'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['email'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
        );
         
        $installer->endSetup();
    }
}
