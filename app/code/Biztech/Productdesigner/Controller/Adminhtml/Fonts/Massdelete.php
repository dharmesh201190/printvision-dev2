<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Fonts;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Biztech\Productdesigner\Model\Mysql4\Fonts\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;
   
    protected $collectionFactory;
    protected $_filesystem;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, \Magento\Framework\Filesystem $filesystem)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
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
            $fontFile = $item->getFontFile();
            $fontImage = $item->getFontImage();
            if($fontFile){
                $fontFile = $dirImg . $fontFile;
                if(file_exists($fontFile)) {
                    unlink($fontFile);
                }
            }
            if($fontImage){
                $fontImage = $dirImg . $fontImage;
                if(file_exists($fontImage)) {
                    unlink($fontImage);
                }
            }
            $item->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 element(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    
}
