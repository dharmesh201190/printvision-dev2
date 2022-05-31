<?php

namespace Biztech\Productdesigner\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;
    protected $_directoryList;
    protected $_fileSystem;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_directoryList = $directoryList;
        $this->_fileSystem = $filesystem;
    }

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);               

		if (version_compare($context->getVersion(), '2.0.1', '<')) {
			$imageEffects = [];
            $imageEffects[] = [
                'effect_name' => 'Grayscale',
                'value' => 0,
                'label' => 'Grayscale',
                'is_filter' => 1
            ];
            $imageEffects[] = [
                'effect_name' => 'Brightness',
                'value' => 0.05,
                'label' => 'Brightness',
                'is_filter' => 0
            ];
            $imageEffects[] = [
                'effect_name' => 'Contrast',
                'value' => 0.5,
                'label' => 'Contrast',
                'is_filter' => 0
            ];
            $imageEffects[] = [
                'effect_name' => 'Sepia',
                'value' => 0,
                'label' => 'Sepia',
                'is_filter' => 1
            ];

            $imageEffects[] = [
                'effect_name' => 'Invert',
                'value' => 0,
                'label' => 'Invert',
                'is_filter' => 1
            ];

            $imageEffects[] = [
                'effect_name' => 'Noise',
                'value' => 700,
                'label' => 'Noise',
                'is_filter' => 0
            ];

            $imageEffects[] = [
                'effect_name' => 'Saturation',
                'value' => 0.7,
                'label' => 'Saturation',
                'is_filter' => 0
            ];

            $imageEffects[] = [
                'effect_name' => 'Vintage',
                'value' => 0,
                'label' => 'vintage',
                'is_filter' => 1
            ];

            $imageEffects[] = [
                'effect_name' => 'Brownie',
                'value' => 0,
                'label' => 'brownie',
                'is_filter' => 1
            ];

	        foreach ($imageEffects as $key => $value) {
                $setup->getConnection()->update(
                    $setup->getTable('productdesigner_imageeffects'),
                    ['is_filter' => $value['is_filter']],
                    ['effect_name = ?' => $value['effect_name']]
                );
	        }
            
        }
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->storeFontData($setup);
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

    protected function storeCsvData($setup, $fileName, $tableName) {
        $fileData = array_map('str_getcsv', file($fileName));
        $tableFields = array_shift($fileData);
        $count = 0;
        $val = implode($tableFields, ",");
        foreach ($fileData as $parentKey => $tableData) {
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
