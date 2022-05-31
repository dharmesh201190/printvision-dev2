<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;

use \Spipu\Html2Pdf\Html2Pdf;

class downloadAllDesignImages extends \Magento\Backend\App\Action {

    protected $_order;
    protected $_designCollection;
    protected $_fileSystem;
    protected $_store;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Biztech\Productdesigner\Model\Mysql4\Designimages\Collection $designCollection,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $store

    ) {
        parent::__construct($context);
        $this->_order = $order;
        $this->_designCollection = $designCollection;
        $this->_fileSystem = $fileSystem;
        $this->_store = $store;
    }

    public function execute() {

        $params = $this->getRequest()->getParams();
        $design_id = $params['design_id'];
        $order_id = $params['order_id'];

        $order_increment_id = $this->_order->load($order_id)->getIncrementId();

        $designImages = $this->_designCollection->addFieldToFilter('design_id', array('eq' => $design_id))->getData();
        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $dir = $reader->getAbsolutePath() . 'productdesigner/designs/'.$design_id . '/';

        $imageFormat =  $this->_store->getValue('productdesigner/general/download_image_format');
        $zip_dir = $reader->getAbsolutePath() . 'productdesigner/designs/'. $design_id . '/zip';

        if (!file_exists($zip_dir)) {
            mkdir($zip_dir, 0777, true);
        }
        $zip_path = array();
        foreach ($designImages as $designImage) {
            if($designImage['design_image_type'] == 'base_high'){

            }
            if ($designImage['design_image_type'] != 'base') {

                $imgName = $imgNameForDownload = $path = "";

                $imgName = !empty(!empty($designImage['image_path']) > 0) ? $designImage['image_path'] : '';

                if($designImage['design_image_type'] != "svg"){
                    if($imageFormat == 'jpg' && !empty($designImage['image_path'])){            
                        $imgNameForDownload = explode('.', basename($imgName))[0] . '.jpg';
                    }if($imageFormat == 'png' && !empty($designImage['image_path'])){
                        $imgNameForDownload = explode('.', basename($imgName))[0] . '.png';         
                    }
                }else{
                    $imgNameForDownload = basename($imgName);
                }
                if($designImage['design_image_type'] == 'base_high'){            
                    $path = $dir. '/base/' .$imgNameForDownload;
                }else if($designImage['design_image_type'] == 'orig_high'){
                    $path = $dir. '/orig/' .$imgNameForDownload;
                }else if($designImage['design_image_type'] == 'svg'){
                    $path = $dir. '/svg/' .$imgNameForDownload;
                }                
                $zip_path[] = $path;
            }
        }
        $pdfdir = $reader->getAbsolutePath() . 'productdesigner/designs/' .$design_id . '/pdf/';
        $pdfArr[]=scandir($pdfdir,1);
        foreach($pdfArr as $files){
            foreach ($files as $key => $value) {
                if ($value != '.' && $value != '..'){
                      $zip_path[]=$pdfdir . $value;
                }
            }
        }
        $name = $zip_dir . '/' . $order_increment_id . '_designs_PNG.zip';
       
        $valid_files = array();
        if (is_array($zip_path)) {
            foreach ($zip_path as $file) {
                if (file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        if (count($valid_files)) {
            if (file_exists($name)) {
                unlink($name);
            }
            $zip = new \ZipArchive();
            if ($zip->open($name, \ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            foreach ($valid_files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
            $new_filename = $order_increment_id . '_designs_JPG.zip';
            header("Content-Type: application/zip");
            header("Content-Length: " . filesize($name));
            header('Content-Disposition: attachment; filename="' . $new_filename . '"');
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($name);
        }
    }


    public function createPdfToZip($content, $pdfPath, $size) {
        $html2pdf = new Html2Pdf('P', $size, 'en');
        $html2pdf->WriteHTML($content);
        $html2pdf->Output($pdfPath, 'F');
    }

}
