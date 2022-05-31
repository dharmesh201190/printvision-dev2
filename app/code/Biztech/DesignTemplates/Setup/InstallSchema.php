<?php

namespace Biztech\DesignTemplates\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
      $installer = $setup;
      $installer->startSetup();

        /**
         * Creating table biztech_productdesigner        */
        $table = $installer->getConnection()->newTable(
          $installer->getTable('productdesigner_designtemplates_category')
              )->addColumn(
                'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Designtemplates Category Id'
              )->addColumn(
                'category_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Category Title'
              )->addColumn(
                'is_root_category', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, ['nullable' => false, 'default' => 0], 'Is Root Category'
              )->addColumn(
                'parent_categories', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false, 'default' => null], 'Parebt Categories'
              )->addColumn(
                'level', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, ['nullable' => false], 'Level'
              )->addColumn(
                'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, ['nullable' => false, 'default' => 0], 'Status'
              )->addColumn(
                'designs', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', ['default' => null, 'nullable' => false], 'Design'
              )->addColumn(
                'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false, 'default' => 0], 'Store id'
              )->addColumn(
                'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false], 'Created At'
              )->addColumn(
                'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Updated At'
              )->setComment(
                'Productdesigner Designtemplates Category'
              );
        $installer->getConnection()->createTable($table);        
        $designtemplates_table = $installer->getConnection()->newTable(
          $installer->getTable('productdesigner_designtemplates')
            )->addColumn(
              'designtemplates_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'designtemplates_id'
            )->addColumn(
              'template_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null], 'template_title'
            )->addColumn(
              'product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'product_id'
            )->addColumn(
              'associated_product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => true], 'associated_product_id'
            )->addColumn(
              'json_objects', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2G', ['default' => null], 'json_objects'
            )->addColumn(
              'image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Image name'
            )->addColumn(
              'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false], 'Created At'
            )->addColumn(
              'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Updated At'
            );
        $installer->getConnection()->createTable($designtemplates_table);
        $tabs = [];
        $tabs[] = [
            'id' => 7,
            'label' => 'Templates',
            'component' => 'templatesComponent',
            'is_admin' => 0,
            'custom_label' => 'Templates',
            'element_id' => '.byi-tooltip-templatesComponent'
        ];

        if (count($tabs)) {
            $installer->getConnection()->insertMultiple($installer->getTable('productdesigner_tabs'), $tabs);
        }
      $installer->endSetup();
    }
  }
