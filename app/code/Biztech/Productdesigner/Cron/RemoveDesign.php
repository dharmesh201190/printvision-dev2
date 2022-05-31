<?php

namespace Biztech\Productdesigner\Cron;

class RemoveDesign {

    protected $_logger;
    protected $_designCollectionFactory;
    protected $_filesystem;
    protected $_store;
    protected $orderModel;
    protected $designFactory;

    public function __construct(\Psr\Log\LoggerInterface $logger, \Biztech\Productdesigner\Model\Mysql4\Designs\CollectionFactory $designCollectionFactory, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\App\Config\ScopeConfigInterface $store, \Magento\Sales\Model\Order $orderModel, \Biztech\Productdesigner\Model\DesignsFactory $designFactory
    ) {
        $this->_logger = $logger;
        $this->_designCollectionFactory = $designCollectionFactory;
        $this->_filesystem = $filesystem;
        $this->_store = $store;
        $this->orderModel = $orderModel;
        $this->designFactory = $designFactory;
    }

    public function execute() {
        $this->_logger->info(__METHOD__);
        $dir = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
        $delete_unused_designs_days = $this->_store->getValue('productdesigner/general/delete_unused_designs');
        $alldesigns = $this->_designCollectionFactory->create()->addFieldToSelect('design_id')->addFieldToSelect('created_at')->addFieldToFilter('customer_id', array('eq' => 0))->getData();
        try {
            foreach ($alldesigns as $designs):
                $date2 = strtotime($designs['created_at']);
                $date1 = time();
                $dateDiff = $date1 - $date2;
                $fullDays = floor($dateDiff / (60 * 60 * 24));
                if ($delete_unused_designs_days <= $fullDays) {
                    $guestorderfound = 0;
                    $orderDatamodel = $this->orderModel->getCollection();
                    foreach ($orderDatamodel as $orderDatamodel1) {
                        $getid = $orderDatamodel1->getData("increment_id");
                        $orderData = $this->orderModel->loadByIncrementId($getid);
                        $orderItems = $orderData->getAllVisibleItems();
                        foreach ($orderItems as $orderItems) {
                            foreach ($orderItems->getData('product_options') as $key => $value) {
                                if ($key == 'additional_options') {
                                    foreach ($value as $val) {
                                        if (isset($val['design_id'])) {
                                            if (($val['design_id'] == $designs['design_id'])) {
                                                $guestorderfound = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($guestorderfound == 0) {
                        $design_id = $designs['design_id'];
                        $designTemplate = $this->designFactory->create()->load($design_id);
                        $imagePath = $dir . 'productdesigner' . DIRECTORY_SEPARATOR . 'designs' . DIRECTORY_SEPARATOR . $design_id;
                        $this->deleteAllImagesFromPath($imagePath);
                        $designTemplate->delete();
                    }
                }
            endforeach;
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }
    }

    public function deleteAllImagesFromPath($path) {
        foreach (glob("{$path}/*") as $file) {
            if (is_dir($file)) {
                $this->deleteAllImagesFromPath($file);
            }
            if (file_exists($file)) {
                unlink($file);
            }
        }
        if (file_exists($path)) {
            rmdir($path);
        }
    }

}
