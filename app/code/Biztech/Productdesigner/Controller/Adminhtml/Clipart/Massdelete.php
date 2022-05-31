<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Clipart;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Biztech\Productdesigner\Model\Mysql4\Clipart\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassDelete extends \Magento\Backend\App\Action
{
   
    protected $filter;
    
    protected $collectionFactory;
    protected $_clipartMediaModel;
    protected $_filesystem;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, \Biztech\Productdesigner\Model\Clipartmedia $clipartMediaModel, \Magento\Framework\Filesystem $filesystem)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_clipartMediaModel = $clipartMediaModel;
        $this->_filesystem = $filesystem;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $dirImg = $reader->getAbsolutePath();
        foreach ($collection as $item) {
            $clipartId = $item->getClipartId();
            $this->deleteClipartMedia($clipartId, $dirImg);
            $item->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 element(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    protected function deleteClipartMedia($clipartId, $dirImg){
        $clipartMediaModel = $this->_clipartMediaModel->getCollection()->addFieldToFilter('clipart_id', array('eq' => $clipartId));
        foreach ($clipartMediaModel->getData() as $clipartMedia) {
            if($clipartMedia['image_path']) {
                $clipartDirPath = $dirImg . 'productdesigner' . DIRECTORY_SEPARATOR . 'clipart';
                $clipartImage =  $clipartDirPath . $clipartMedia['image_path'];
                if(file_exists($clipartImage)) {
                    unlink($clipartImage);
                }
                $resizedImg = $clipartDirPath . DIRECTORY_SEPARATOR . 'resized' . $clipartMedia['image_path'];
                $mediumImg = $clipartDirPath . DIRECTORY_SEPARATOR . 'medium' . $clipartMedia['image_path'];
                if (file_exists($resizedImg)) {
                    unlink($resizedImg) or print_r(error_get_last());
                }
                if (file_exists($mediumImg)) {
                    unlink($mediumImg) or print_r(error_get_last());
                }
            }
        }
    }
}
