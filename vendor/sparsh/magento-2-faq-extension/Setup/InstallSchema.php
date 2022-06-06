<?php
/**
 * Class InstallSchema
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_Faq
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\Faq\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @category Sparsh
 * @package  Sparsh_Faq
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup   Setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context Context
     *
     * @return void
     *
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
       
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sparsh_faq')
        )->addColumn(
            'faq_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ],
            'Faq Id'
        )->addColumn(
            'faq_category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [
                "unsigned" => true,
                'default' => '1'
            ],
            'Faq Category Id'
        )->addColumn(
            'faq_question',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'nullable' => false
            ],
            'Faq Question'
        )->addColumn(
            'faq_answer',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [
                'nullable' => false
            ],
            'Faq Answer'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false,
                'default' => '1'
            ],
            'Is Faq Active'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'default' => '0'
            ],
            'Faq Sort Order'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
            ],
            'Faq Created DateTime'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
            ],
            'Faq Modified DateTime'
        )->addIndex(
            $installer->getIdxName(
                'sparsh_faq',
                ['faq_question'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['faq_question'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->addIndex(
            $installer->getIdxName(
                'sparsh_faq',
                ['faq_answer'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['faq_answer'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'sparsh FAQ Table'
        );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('sparsh_faq_store')
        )->addColumn(
            'faq_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ],
            'Primary Key'
        )->addColumn(
            'faq_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false, 'unsigned' => true
            ],
            'Faq Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false, 'unsigned' => true
            ],
            'Store Id'
        )->addForeignKey(
            $installer->getFkName(
                'sparsh_faq_key',
                'faq_id',
                'sparsh_faq',
                'faq_id'
            ),
            'faq_id',
            $installer->getTable('sparsh_faq'),
            'faq_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'sparsh_faq_store_key',
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Sparsh FAQ Store Table'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('sparsh_faq_category')
        )->addColumn(
            'faq_category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ],
            'Faq Category Id'
        )->addColumn(
            'faq_category_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'nullable' => false
            ],
            'Faq Category Name'
        )->addColumn(
            'faq_category_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            [
                'nullable' => true
            ],
            'Faq Category Description'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false,
                'default' => '1'
            ],
            'Status'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
            ],
            'Created DateTime'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
            ],
            'Modified DateTime'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['default' => '0'],
            'Faq Category Sort Order'
        )->addIndex(
            $installer->getIdxName(
                'sparsh_faq_category',
                ['faq_category_name']
            ),
            ['faq_category_name']
        )->setComment(
            'Sparsh FAQ Category Table'
        );

        $installer->getConnection()->createTable($table);

        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'sparsh_faq',
                'faq_category_id',
                $installer->getTable('sparsh_faq_category'),
                'faq_category_id'
            ),
            $installer->getTable('sparsh_faq'),
            'faq_category_id',
            $installer->getTable('sparsh_faq_category'),
            'faq_category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_RESTRICT
        );
        $installer->getConnection();

        $installer->endSetup();
    }
}
