<?php

/**
 * @category   Biztech
 * @package    Biztech_BackgroundPatterns
 * @author     developer1.test@gmail.com
 * @copyright  This file was generated by using Module Creator(http://code.vky.co.in/magento-2-module-creator/) provided by VKY <viky.031290@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Biztech\EditablePdf\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Add column is_pattern in productdesigner_clipart table 
         */
        if ($installer->tableExists($installer->getTable('productdesigner_printablecolor'))) {
            $printablecolortable = $installer->getTable('productdesigner_printablecolor');

            $installer->getConnection()->addColumn($printablecolortable, 'color_c', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Cyan'
            ]);

            $installer->getConnection()->addColumn($printablecolortable, 'color_m', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Magenta'
            ]);

            $installer->getConnection()->addColumn($printablecolortable, 'color_y', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Yellow'
            ]);

            $installer->getConnection()->addColumn($printablecolortable, 'color_k', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Black'
            ]);

            $installer->getConnection()->addColumn($printablecolortable, 'pantone_color', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Pantone Color'
            ]);

        }
        $installer->endSetup();
    }

}
