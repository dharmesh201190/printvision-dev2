<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;

use \Spipu\Html2Pdf\Html2Pdf;

class downloadAllImages extends \Magento\Backend\App\Action {
 
    protected $_order;
    protected $_designCollection;
    protected $_fileSystem;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Biztech\Productdesigner\Model\Mysql4\Designimages\Collection $designCollection,
        \Magento\Framework\Filesystem $fileSystem
    ) {
        parent::__construct($context);
        $this->_order = $order;
        $this->_designCollection = $designCollection;
        $this->_fileSystem = $fileSystem;

    }
 

    public function execute() {
        $params = $this->getRequest()->getParams();
        $order_id = $params['order_id'];
        $design_id = $params['design_id'];
        $order_increment_id = $this->_order->load($order_id)->getIncrementId();
        $name = $order_increment_id . '_designs.pdf'; 
        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $pdfdir = $reader->getAbsolutePath() . 'productdesigner/designs/' .$design_id . '/pdf/' . $name;
        $this->getResponse()
        ->setHeader('Content-Disposition', 'attachment; filename=' . basename($pdfdir))
        ->setHeader('Content-Length', filesize($pdfdir))
        ->setHeader('Content-type', 'pdf');
        $this->getResponse()->sendHeaders();
        readfile($pdfdir);
       
    }

}
