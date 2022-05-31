<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;

class downloadDesignImage extends \Magento\Backend\App\Action {

    protected $_order;
    protected $_designCollection;
    protected $_fileSystem;
    protected $_store;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Sales\Model\Order $order, \Biztech\Productdesigner\Model\Mysql4\Designimages\Collection $designCollection, \Magento\Framework\Filesystem $fileSystem, \Magento\Framework\App\Config\ScopeConfigInterface $store
    ) {
        parent::__construct($context);
        $this->_order = $order;
        $this->_designCollection = $designCollection;
        $this->_fileSystem = $fileSystem;
        $this->_store = $store;
    }

    public function execute() {

        $params = $this->getRequest()->getParams();
        $order_id = $params['order_id'];
        $image_id = $params['image_id'];
        $design_id = $params['design_id'];
        $item_id = isset($params['item_id']) ? $params['item_id']: null;
        $designImage = $this->_designCollection->addFieldToFilter('image_id', array('eq' => $image_id))->getData();
        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $dir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id;
        $imageFormat = $this->_store->getValue('productdesigner/general/download_image_format');
        $imgNameForDownload = $path = $imgName = "";
        $imgName = !empty(sizeof($designImage) > 0) ? $designImage[0]['image_path'] : '';
        if ($imageFormat == 'jpg' && !empty($designImage[0]['image_path'])) {
            $imgNameForDownload = explode('.', basename($imgName))[0] . '.jpg';
        } else if ($imageFormat == 'png' && !empty($designImage[0]['image_path'])) {
            $imgNameForDownload = explode('.', basename($imgName))[0] . '.png';
        }
        if ($designImage[0]['design_image_type'] == 'base_high' || $designImage[0]['design_image_type'] == 'basenamenumber') {
            $path = $dir . '/base/' . $imgNameForDownload;
        } else if ($designImage[0]['design_image_type'] == 'svg') {
            $path = $dir . '/svg/' . $imgName;
        } else {
            $path = $dir . '/orig/' . $imgNameForDownload;
        }

        if (file_exists($path)) {
            $imgtype = getimagesize($path);
            $contentType = $imgtype['mime'];
            $this->getResponse()
                    ->setHeader('Content-Disposition', 'attachment; filename=' . basename($path))
                    ->setHeader('Content-Length', filesize($path))
                    ->setHeader('Content-type', $contentType);
            $this->getResponse()->sendHeaders();
            readfile($path);
        } else {
            $this->messageManager->addError("Image not found");
            if(isset($image_id)){
                $this->_redirect("productdesigner/Productdesigner/viewDesign/design_id/" . $designImage[0]['design_id'] . "/order_id/" . $order_id . '/item_id/'. $item_id);
            }else{
                $this->_redirect("productdesigner/Productdesigner/viewDesign/design_id/" . $designImage[0]['design_id'] . "/order_id/" . $order_id);
            }
        }
    }

}
