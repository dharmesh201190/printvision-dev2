<?php
namespace Biztech\EditablePdf\Controller\Adminhtml\Productdesigner;

class downloadPdf extends \Magento\Backend\App\Action {
    protected $_fileSystem;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $fileSystem

    ) {
        parent::__construct($context);
        $this->_fileSystem = $fileSystem;
    }

    public function execute() {
        ob_start();
        $params = $this->getRequest()->getParams();
        $order_id = $params['order_id'];
        $design_id = $params['design_id'];
        $item_id = $params['item_id'];
        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $pdfPath = $reader->getAbsolutePath() . 'productdesigner/designs/'.$design_id.'/pdf/';
        $pdfName = $design_id.'_designs_vector.pdf';
        $finalPath = $pdfPath . $pdfName;
        if(file_exists($finalPath)){
            $this->getResponse()
                 ->setHeader('Content-Disposition', 'attachment; filename=' . basename($finalPath))
                 ->setHeader('Content-Length', filesize($finalPath))
                 ->setHeader('Content-type', 'pdf');
            $this->getResponse()->sendHeaders();
            readfile($finalPath);
        }else{
            $this->messageManager->addError(__('This item no longer exists.'));
            $viewdesign_url = 'productdesigner/Productdesigner/viewDesign/design_id/'.$design_id.'/order_id/'.$order_id.'/item_id/'.$item_id; 
            $this->_redirect($viewdesign_url);
            return;
        }
    }
}
