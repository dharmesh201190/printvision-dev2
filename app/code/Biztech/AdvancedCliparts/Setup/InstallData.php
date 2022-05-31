<?php

namespace Biztech\AdvancedCliparts\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Archive\Zip as ZipArchive;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;
    protected $_directoryList;
    protected $_fileSystem;
    protected $clipartFactory;

    public function __construct(
    EavSetupFactory $eavSetupFactory, \Magento\Framework\App\Filesystem\DirectoryList $directoryList, \Magento\Framework\Filesystem $filesystem, \Biztech\Productdesigner\Model\ClipartFactory $clipartFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_directoryList = $directoryList;
        $this->_fileSystem = $filesystem;
        $this->clipartFactory = $clipartFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $this->storeClipartData($setup);
    }

    protected function storeClipartData($setup) {
        $appPath = $this->_directoryList->getPath('app');
        $sampleDataPath = $appPath . "/code/Biztech/AdvancedCliparts/SampleData/";
        $clipartCategoryFile = $sampleDataPath . "clipartCategoryData.csv";
        $clipartMediaFile = $sampleDataPath . "clipartMediaData.csv";
        $clipartCategoryTable = $setup->getTable("productdesigner_clipart");
        $clipartMediaTable = $setup->getTable("productdesigner_clipartmedia");
        $this->storeCsvData($setup, $clipartCategoryFile, $clipartCategoryTable);
        $this->storeCsvData($setup, $clipartMediaFile, $clipartMediaTable);
        $this->storeMediaData($sampleDataPath, $clipartMediaFile);
    }

    protected function storeCsvData($setup, $fileName, $tableName) {
        $fileData = array_map('str_getcsv', file($fileName));
        $tableFields = array_shift($fileData);
        $count = 0;
        $val = implode($tableFields, ",");
        foreach ($fileData as $parentKey => $tableData) {
            
            if ($tableName == $setup->getTable("productdesigner_clipartmedia")) {
                $ClipartCategory = $this->clipartFactory->create()->getCollection()->addFieldToFilter('clipart_title', $tableData[0]);
                foreach ($ClipartCategory as $clipartId) {
                    $id = $clipartId->getClipartId();
                    $tableData[0] = $id;
                }
            } else if ($tableName == $setup->getTable("productdesigner_clipart")) {
                $ClipartCategory = $this->clipartFactory->create()->getCollection()->addFieldToFilter('clipart_title', $tableData[2]);
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

    protected function storeMediaData($sampleDataPath, $clipartMediaFile) {
        if (file_exists($clipartMediaFile)) {
            $file = $sampleDataPath . "clipart.zip";
            $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $extractPath = $reader->getAbsolutePath() . 'productdesigner/';
            $path = $reader->getAbsolutePath();
            if (!is_dir($path . 'productdesigner')) {
                mkdir($path . "productdesigner");
            }
            if (!is_dir($extractPath . "clipart")) {
                mkdir($extractPath . "clipart");
            }
            $this->unpack($file, $extractPath);
        }
    }

    public function unpack($source, $destination) {
        $zip = new \ZipArchive();
        $res = $zip->open($source);
        if ($res === TRUE) {
            $zip->extractTo($destination);
            $zip->close();
        }
    }

}
