<?php

namespace Biztech\Productdesigner\Observer;

use Magento\Framework\Event\ObserverInterface;

class removeOrderdesign implements ObserverInterface {

    protected $_fileSystem;
    protected $_removeDesigns;
    protected $_helper;
    protected $_design;

    public function __construct(
    \Magento\Framework\Filesystem $fileSystem, \Biztech\Productdesigner\Cron\RemoveDesign $removeDesigns, \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Model\Designs $design
    ) {
        $this->_fileSystem = $fileSystem;
        $this->_removeDesigns = $removeDesigns;
        $this->_helper = $helper;
        $this->_design = $design;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $item = $observer->getQuoteItem();
        $params = $item->getProduct()->getCustomOptions();
        if (!empty($params['additional_options'])) {
            $getDeignData = $params['additional_options']->getValue();
            if (!empty($getDeignData)) {
                $getDesignIds = $this->_helper->unserializeData($getDeignData);
                foreach ($getDesignIds as $key => $value) {
                    $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $design_id = !empty($value['design_id']) ? $value['design_id'] : 0;
                    $designData = $this->_design->load($design_id)->getData();
                    if (isset($designData['customer_id']) && $designData['customer_id'] == 0) {
                        $dir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/';
                        $this->_removeDesigns->deleteAllImagesFromPath($dir);
                    }
                }
            }
        }
    }

}
