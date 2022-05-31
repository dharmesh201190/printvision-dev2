<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;


class downloadImage extends \Magento\Backend\App\Action {

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
        $image_id = $params['image_id'];
        $design_id = $params['design_id'];
        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $pdfPath = $reader->getAbsolutePath() . 'productdesigner/designs/'.$design_id.'/pdf';
        $designImages =$this->_designCollection->addFieldToFilter('image_id', array('eq' => $image_id))->getData();
        $name = $designImages[0]['image_path'];
        $last_dot_index = strrpos($name, ".");
        $pdfName = substr($name, 0, $last_dot_index);
        $finalPath = $pdfPath . $pdfName .'.pdf';
        $this->getResponse()
             ->setHeader('Content-Disposition', 'attachment; filename=' . basename($finalPath))
             ->setHeader('Content-Length', filesize($finalPath))
             ->setHeader('Content-type', 'pdf');
        $this->getResponse()->sendHeaders();
        readfile($finalPath);
    }
}
