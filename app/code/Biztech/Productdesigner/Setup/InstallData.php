<?php

namespace Biztech\Productdesigner\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Archive\Zip as ZipArchive;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;
    protected $_directoryList;
    protected $clipartCategoriesCollection;
    protected $_fileSystem;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList, \Biztech\Productdesigner\Model\Mysql4\Clipart\CollectionFactory $clipartCategoriesCollection, \Magento\Framework\Filesystem $filesystem
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_directoryList = $directoryList;
        $this->clipartCategoriesCollection = $clipartCategoriesCollection;
        $this->_fileSystem = $filesystem;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);               
        $this->storeClipartData($setup);
        $this->storeFontData($setup);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'enable_product_designer', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Product Designer',
                'input' => 'boolean',
                'class' => '',
                'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable,simple',
                'required' => false,
                'group' => 'Product Designer'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'customized_products_price', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Additional Price for Customized Products',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable,simple',
                'required' => false,
                'group' => 'Product Designer'
            ]
        );
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'added_text_price', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Additional Cost per Added Text',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable,simple',
                'required' => false,
                'group' => 'Product Designer'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'added_image_price', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Additional Cost per Added Image',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable,simple',
                'required' => false,
                'group' => 'Product Designer'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'custom_added_image_price', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Additional Cost per Custom Uploaded Image',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable,simple',
                'required' => false,
                'group' => 'Product Designer'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'default_associated_product', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'attribute_set' => '',
                'label' => 'Default Associated Product',
                'input' => 'select',
                'visible' => true,
                'required' => false,
                'apply_to' => 'configurable',
                'source' => 'Biztech\Productdesigner\Model\Entity\Attribute\Source\AssociatedProducts',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'group' => 'Product Designer'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'default_clipart_category', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Set Default Clipart Category',
                'attribute_set' => '',
                'input' => 'select',
                'visible' => true,
                'required' => false,
                'apply_to' => 'configurable,simple',
                'source' => 'Biztech\Productdesigner\Model\Entity\Attribute\Source\ClipartCategories',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'group' => 'Product Designer'
            ]
        );

        //Insert Tabs in tabel
        $tabs = [];
        $tabs[] = [
            'id' => 1,
            'label' => 'Plain Text',
            'component' => 'plainTextComponent',
            'is_admin' => 1,
            'custom_label' => 'Plain Text',
            'element_id' => '.byi-tooltip-plainTextComponent,.byi-tooltip-font'                
        ];
        $tabs[] = [
            'id' => 2,
            'label' => 'Clip Art',
            'component' => 'clipartComponent',
            'is_admin' => 1,
            'custom_label' => 'Clip Art',
            'element_id' => '.byi-tooltip-clipartComponent,.byi-tooltip-image-effects'                
        ];
        $tabs[] = [
            'id' => 3,
            'label' => 'Upload',
            'component' => 'imageUploadComponent',
            'is_admin' => 1,
            'custom_label' => 'Upload',
            'element_id' => '.byi-tooltip-imageUploadComponent,.byi-tooltip-image-effects'                
        ];
        $tabs[] = [
            'id' => 4,
            'label' => 'Background',
            'component' => 'backgroundColorComponent',
            'is_admin' => 1,
            'custom_label' => 'Background',
            'element_id' => '.byi-tooltip-backgroundColorComponent'                
        ];

        $tabs[] = [
            'id' => 5,
            'label' => 'My Designs',
            'component' => 'designsComponent',
            'is_admin' => 0,
            'custom_label' => 'My Designs',
            'element_id' => '.byi-tooltip-designsComponent'                
        ];
        if (count($tabs)) {
            $setup->getConnection()
            ->insertMultiple($setup->getTable('productdesigner_tabs'), $tabs);
        }

        //Insert Image effect
        $imageEffects = [];
        $imageEffects[] = [
            'effect_name' => 'Grayscale',
            'value' => 0,
            'label' => 'Grayscale',
            'effect_image' => 'productdesigner/effects/Grayscale.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Brightness',
            'value' => 0.05,
            'label' => 'Brightness',
            'effect_image' => 'productdesigner/effects/Brightness.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Contrast',
            'value' => 0.5,
            'label' => 'Contrast',
            'effect_image' => 'productdesigner/effects/Contrast.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Sepia',
            'value' => 0,
            'label' => 'Sepia',
            'effect_image' => 'productdesigner/effects/Sepia.png'
        ];

        $imageEffects[] = [
            'effect_name' => 'Invert',
            'value' => 0,
            'label' => 'Invert',
            'effect_image' => 'productdesigner/effects/Invert.png'
        ];

        $imageEffects[] = [
            'effect_name' => 'Noise',
            'value' => 700,
            'label' => 'Noise',
            'effect_image' => 'productdesigner/effects/Noise.png'
        ];

        $imageEffects[] = [
            'effect_name' => 'Saturation',
            'value' => 0.7,
            'label' => 'Saturation',
            'effect_image' => 'productdesigner/effects/Saturation.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Vintage',
            'value' => 0,
            'label' => 'vintage',
            'effect_image' => 'productdesigner/effects/Vintage.png'
        ];
        $imageEffects[] = [
            'effect_name' => 'Brownie',
            'value' => 0,
            'label' => 'brownie',
            'effect_image' => 'productdesigner/effects/Brownie.png'
        ];
        if (count($imageEffects)) {
            $setup->getConnection()
            ->insertMultiple($setup->getTable('productdesigner_imageeffects'), $imageEffects);
            $this->storeImageEffectImages();
        }

        //enable_handles attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'enable_handles', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Handles outside the design area',
                'input' => 'boolean',
                'class' => '',
                'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'apply_to' => 'configurable,simple',
                'required' => false,
                'group' => 'Product Designer',
                'note' => 'Select Yes to allow handles outside the canvas. It will work for single design area only.'
            ]
        );

        //allow_add_more attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'allow_add_more', [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Allow multiple products in add to cart',
                'attribute_set' => '',
                'input' => 'boolean',
                'class' => '',
                'required' => false,
                'visible' => true,
                'default' => '1',
                'apply_to' => 'configurable',
                'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'group' => 'Product Designer'
            ]
        );
    }

    protected function storeClipartData($setup) {
        $appPath = $this->_directoryList->getPath('app');
        $sampleDataPath = $appPath . "/code/Biztech/Productdesigner/SampleData/clipart/";
        $clipartCategoryFile = $sampleDataPath . "clipartCategoryData.csv";
        $clipartMediaFile = $sampleDataPath . "clipartMediaData.csv";
        $clipartCategoryTable = $setup->getTable("productdesigner_clipart");
        $clipartMediaTable = $setup->getTable("productdesigner_clipartmedia");
        $this->storeCsvData($setup, $clipartCategoryFile, $clipartCategoryTable);
        $this->storeCsvData($setup, $clipartMediaFile, $clipartMediaTable);
        $this->storeMediaData($sampleDataPath,$clipartMediaFile);
    }

    protected function storeCsvData($setup, $fileName, $tableName) {
        $fileData = array_map('str_getcsv', file($fileName));
        $tableFields = array_shift($fileData);
        $count = 0;
        $val = implode($tableFields, ",");
        foreach ($fileData as $parentKey => $tableData) {
            
             if ($tableName == $setup->getTable("productdesigner_clipartmedia")) {
                $ClipartCategory = $this->clipartCategoriesCollection->create()->addFieldToFilter('clipart_title', $tableData[0]);
                foreach ($ClipartCategory as $clipartId) {
                    $id = $clipartId->getClipartId();
                    $tableData[0] = $id;
                }
            } else if ($tableName == $setup->getTable("productdesigner_clipart")) {
                $ClipartCategory = $this->clipartCategoriesCollection->create()->addFieldToFilter('clipart_title', $tableData[2]);
                foreach ($ClipartCategory as $clipartId) {
                    $id = $clipartId->getClipartId();
                    $tableData[2] = $id;
                }
            }

            $count = 0;            
            $data = array();
            foreach ($tableData as $childKey => $tableFieldValue) {           
                $fieldName = $tableFields[$count++];
                $data[$fieldName] = $tableFieldValue;
            }
            if (count($data)) {
                $setup->getConnection()
                ->insertMultiple($setup->getTable($tableName), $data);
            }
        }
    }

    protected function storeMediaData($sampleDataPath,$clipartMediaFile) {
        if(file_exists($clipartMediaFile)){
            $file = $sampleDataPath . "clipart.zip";
            $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $extractPath = $reader->getAbsolutePath() . 'productdesigner/';
            $path =$reader->getAbsolutePath();
            if(!is_dir($path. 'productdesigner')){
                mkdir($path . "productdesigner");
            }
            if (!is_dir($extractPath . "clipart")) {
                mkdir($extractPath . "clipart");
            }
            $this->unpack($file, $extractPath);
        }
    }

    protected function storeFontData($setup) {
        $appPath = $this->_directoryList->getPath('app');
        $path = $appPath . "/code/Biztech/Productdesigner/SampleData/fonts";
        $fontData = $path . "/standard.csv";
        $fontTableName = "productdesigner_fonts";
        if(file_exists($fontData)){
            $this->storeCsvData($setup, $fontData, $fontTableName);
            $file = $path . "/standard.zip";
            $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $path =$reader->getAbsolutePath();
            $extractPath = $reader->getAbsolutePath() . 'productdesigner/';
            if(!is_dir($path. 'productdesigner')){
                mkdir($path . "productdesigner");
            }
            if (!is_dir($extractPath . "fonts")) {
                mkdir($extractPath . "fonts", 0777);
            }
            $this->unpack($file, $extractPath. "fonts/");
        }
    }

    protected function storeImageEffectImages() {
        $appPath = $this->_directoryList->getPath('app');
        $sampleDataPath = $appPath . "/code/Biztech/Productdesigner/SampleData/";
        $file = $sampleDataPath . "effects.zip";
        if(file_exists($file)) {
            $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $extractPath = $reader->getAbsolutePath() . 'productdesigner/';
            $path =$reader->getAbsolutePath();
            if(!is_dir($path. 'productdesigner')){
                mkdir($path . "productdesigner");
            }
            $this->unpack($file, $extractPath);
        }
    }
    
    public function unpack($source, $destination)
    {
        $zip = new \ZipArchive();
        $res = $zip->open($source);
        if ($res === TRUE) {
          $zip->extractTo($destination);
          $zip->close();
      }
  }

}
