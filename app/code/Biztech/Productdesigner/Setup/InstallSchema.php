<?php


namespace Biztech\Productdesigner\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

   
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        $this->createClipartTable($installer);


        //Start productdesigner_tabs table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_tabs')
                )->addColumn(
                    'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Primary key of productdesigner_tabs table'
                )->addColumn(
                    'label', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null], 'Label'
                )->addColumn(
                    'component', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null], 'Component name'
                )->addColumn(
                    'is_admin', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false, 'default' => 1], 'Is Admin'
                )->addColumn(
                    'custom_label', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null], 'Custom Label'
                )->addColumn(
                    'first_tooltip', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'First tooltip'
                )->addColumn(
                    'second_tooltip', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Second tooltip'
                )->addColumn(
                    'element_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Element id'
                )->setComment(
                    'Product Designer Tabs'
                );
        $installer->getConnection()->createTable($table);
        //End productdesigner_tabs table

        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_imageeffects')
                )
                ->addColumn(
                    'effect_id', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
                    10, 
                    [
                        'identity' => true, 
                        'unsigned' => true, 
                        'nullable' => false, 
                        'primary' => true
                    ], 
                    'Primary key of productdesigner_imageeffects table'
                )->addColumn(
                    'effect_name', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                    255, 
                    [
                        'nullable' => false, 
                        'default' => null
                    ], 
                    'Image effect name'
                )->addColumn(
                    'value', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                    255, 
                    [
                        'nullable' => true, 
                        'default' => 0
                    ], 
                    'Effect Value'
                )->addColumn(
                    'effect_image', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                    255, 
                    [
                        'nullable' => true, 
                        'default' => null
                    ], 
                    'Effected image'
                )->addColumn(
                    'label', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                    255, 
                    [
                        'nullable' => true, 
                        'default' => null
                    ], 
                    'Display Label'
                )->addColumn(
                    'status', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
                    10, 
                    [
                        'nullable' => false, 
                        'default' => 1
                    ], 
                    'Effect status'
                )->setComment(
                    'Product Designer Image Effects List Table'
                );
        $installer->getConnection()->createTable($table);


        //Start Sub Tabs Table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_subtabs')
                )->addColumn(
                    'subtabs_id', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
                    11, 
                    [
                        'identity' => true, 
                        'unsigned' => true, 
                        'nullable' => false, 
                        'primary' => true
                    ], 
                    'Primary key of productdesigner_subtabs table'
                )->addColumn(
                    'maintab',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'default' => null,
                        'nullable' => false
                    ],
                    'Maintab Id'
                )->addColumn(
                    'subtabs',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                        'default' => null
                    ], 
                    'Subtabs Id'
                )->setComment(
                    'Product Designer Sub Tabs Table'
                );
        $installer->getConnection()->createTable($table);
        //End Sub Tabs Table

        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_fonts')
                )->addColumn(
                    'fonts_id', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
                    11, 
                    [
                        'identity' => true, 
                        'unsigned' => true, 
                        'nullable' => false, 
                        'primary' => true
                    ], 
                    'Primary key of productdesigner_fonts table'
                )->addColumn(
                    'font_label', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                    255, 
                    [
                        'default' => null, 
                        'nullable' => false
                    ], 
                    'Font label'
                )->addColumn(
                    'font_file', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                    255, 
                    [
                        'default' => null, 
                        'nullable' => false
                    ], 
                    'Font file path'
                )->addColumn(
                    'position', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    [
                        'default' => null,
                        'unsigned' => true
                    ],
                    'Font position'
                )->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    11,
                    [
                        'default' => 0,
                        'nullable' => false
                    ],
                    'Font status'
                )->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'default' => null,
                        'nullable' => false
                    ],
                    'Font store id'
                )->addColumn(
                    'font_image',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'default' => null,
                        'nullable' => false
                    ],
                    'Font image path'
                )->setComment(
                    'Product Designer Fonts Table'
                );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_printablecolor')
                )->addColumn(
                    'printablecolor_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Primary Key of productdesigner_printablecolor table'
                )->addColumn(
                    'color_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false, 'default' => 0], 'Color name'
                )->addColumn(
                    'color_code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false, 'default' => 0], 'Color code'
                )->addColumn(
                    'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['nullable' => false, 'default' => 0], 'Color status'
                )->addColumn(
                    'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => 0], 'Color store id'
                )->setComment(
                'Productdesigner Printable Colors'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_imageside')
                )->addColumn(
                    'imageside_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Primary key of productdesigner_imageside table'
                )->addColumn(
                    'imageside_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Image side title'
                )->addColumn(
                    'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, ['nullable' => false, 'default' => 0], 'Image side status'
                )->setComment(
                'Product Designer Image Side Table'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_image_selection_area')
                )->addColumn(
                    'design_area_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Primary key of productdesigner_image_selection_area table'
                )->addColumn(
                    'image_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'image_id'
                )->addColumn(
                    'selection_area', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'selection_area'
                )->addColumn(
                    'product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'product_id'
                )->addColumn(
                    'masking_image_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'masking_image_id'
                )->addColumn(
                    'imageside_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'imageside_id'
                )->setComment(
                    'Product Designer Image Selection Area Table'
                );
        $installer->getConnection()->createTable($table);

        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_image_selection_area')}` ADD INDEX ( `image_id` );");
        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_image_selection_area')}` ADD FOREIGN KEY ( `image_id` ) REFERENCES `{$installer->getTable('catalog_product_entity_media_gallery')}` (`value_id`) ON DELETE CASCADE ON UPDATE CASCADE ;");
        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_image_selection_area')}` ADD FOREIGN KEY ( `product_id` ) REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE;");
        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_product_entity_media_gallery_value'),
            'image_side', 
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Image Side',
            ]
        );

        //Start productdesigner_masking table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_masking')
                )->addColumn(
                    'masking_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'masking_id'
                )->addColumn(
                    'masking_path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Masking Path'
                )->addColumn(
                    'masking_label', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Masking Label'
                )->setComment(
                    'Product Designer Masking Table'
                );
        $installer->getConnection()->createTable($table);
        //End productdesigner_masking table

        //Start productdesigner_designs table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_designs')
                )->addColumn(
                    'design_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Design Id'
                )->addColumn(
                    'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null], 'Design Title'
                )->addColumn(
                    'product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'Product Id'
                )->addColumn(
                    'associated_product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'Associated Product Id'
                )->addColumn(
                    'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true], 'Customer Id'
                )->addColumn(
                    'prices', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', ['default' => null], 'Prices'
                )->addColumn(
                    'json_objects', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2G', ['default' => null], 'Canvas objects json'
                )->addColumn(
                    'scale_factor', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null], 'Scale factor of saved design'
                )->addColumn(
                    'canvas_dataurl_file', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null], 'File name of Canvas DataURL saved'
                )->addColumn(
                    'relative_image_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Related image ids'
                )->addColumn(
                    'parent_image_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Parent image ids'
                )->addColumn(
                    'customer_comments', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', ['default' => null], 'Customer comments'
                )->addColumn(
                    'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Created At'
                )->addColumn(
                    'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Updated At'
                )->setComment(
                    'Product Designer Designs Table'
                );
        $installer->getConnection()->createTable($table);
        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_designs')}` ADD INDEX ( `product_id` )");
        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_designs')}` ADD FOREIGN KEY ( `product_id` ) REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE ");
        //End productdesigner_designs table

        //Start productdesigner_design_images table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_design_images')
                )->addColumn(
                    'image_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Image Id of design'
                )->addColumn(
                    'design_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'Design Id'
                )->addColumn(
                    'product_image_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'Image Id of product image'
                )->addColumn(
                    'design_image_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Design image type of design'
                )->addColumn(
                    'image_path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Path of generated image'
                )->setComment(
                    'Product Designer Design Images Table'
                );
        $installer->getConnection()->createTable($table);
        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_design_images')}` ADD INDEX ( `design_id` )");
        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_design_images')}` ADD FOREIGN KEY ( `design_id` ) REFERENCES `{$installer->getTable('productdesigner_designs')}` (`design_id`) ON DELETE CASCADE ON UPDATE CASCADE");
        //End productdesigner_design_images table

        // Start productdesigner_order table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_order')
                )->addColumn(
                    'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id'
                )->addColumn(
                    'order_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Order id'
                )->addColumn(
                    'design_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Design id'
                )->addColumn(
                    'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, ['nullable' => false, 'default' => 0], 'Status'
                )->setComment(
                    'Product Designer Design Images Table'
                );
        $installer->getConnection()->createTable($table);
        // End productdesigner_order table

        //Start productdesigner_uploaded_image table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('productdesigner_uploaded_image')
                )->addColumn(
                    'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Primary Key of productdesigner_uploaded_image table'
                )->addColumn(
                    'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['nullable' => false, 'default' => 0], 'Customer Id')
                ->addColumn(
                    'image_path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false, 'default' => 0], 'Image Path');
                $installer->getConnection()->createTable($table);
        
        //End productdesigner_uploaded_image table
        $installer->endSetup();
    }

    protected function createClipartTable($installer) {
        if (!$installer->tableExists($installer->getTable('productdesigner_clipart'))) {
            $table = $installer->getConnection()
                    ->newTable($installer->getTable('productdesigner_clipart'))
                    ->addColumn(
                            'clipart_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Clipart Id'
                    )
                    ->addColumn(
                            'clipart_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Clipart Title'
                    )
                    ->addColumn(
                            'is_root_category', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, ['nullable' => false, 'default' => 0], 'Is Root Category'
                    )
                    ->addColumn(
                            'parent_categories', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false, 'default' => null], 'Parent Categories'
                    )
                    ->addColumn(
                            'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, ['nullable' => false, 'default' => 0], 'Status'
                    )->addColumn(
                            'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 10, ['nullable' => false, 'default' => '0'], 'Store Id')
                    ->addColumn(
                    'level', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['nullable' => false, 'default' => 0], 'Level'
            );

            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists($installer->getTable('productdesigner_clipartmedia'))) {
            $table = $installer->getConnection()
                    ->newTable($installer->getTable('productdesigner_clipartmedia'))
                    ->addColumn(
                            'image_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Image Id'
                    )->addColumn(
                            'clipart_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'Clipart Id'
                    )
                    ->addColumn(
                            'image_path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Image Path'
                    )
                    ->addColumn(
                            'label', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Label'
                    )
                    ->addColumn(
                            'tags', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Tags'
                    )
                    ->addColumn(
                            'position', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['default' => null, 'unsigned' => true], 'Position'
                    )
                    ->addColumn(
                            'disabled', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, ['default' => 0, 'nullable' => false], 'Disabled'
                    )
                    ->addColumn(
                    'price', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => false], 'Price'
            );
            $installer->getConnection()->createTable($table);
        }

        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_clipartmedia')}` ADD INDEX ( `clipart_id` ); ");
        $installer->run("ALTER TABLE `{$installer->getTable('productdesigner_clipartmedia')}` ADD FOREIGN KEY ( `clipart_id` ) REFERENCES `{$installer->getTable('productdesigner_clipart')}` (`clipart_id`) ON DELETE CASCADE ON UPDATE CASCADE ; ");
    }

}
