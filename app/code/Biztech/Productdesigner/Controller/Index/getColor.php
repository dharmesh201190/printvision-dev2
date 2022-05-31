<?php

namespace Biztech\Productdesigner\Controller\Index;

class getColor extends \Magento\Framework\App\Action\Action {

    protected $_storeManager;
    protected $_helper;
    protected $_assetRepo;
    protected $_dir;
    protected $request;
    protected $_logger;
    protected $_infoHelper;
    protected $printableColorFactory;
    protected $printableColorCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager,  \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Framework\Filesystem\DirectoryList $dir, \Magento\Framework\App\Request\Http $request, \Psr\Log\LoggerInterface $logger, \Biztech\Productdesigner\Model\PrintablecolorFactory $printableColorFactory, \Biztech\Productdesigner\Model\Mysql4\Printablecolor\CollectionFactory $printableColorCollection
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_assetRepo = $assetRepo;
        $this->_dir = $dir;
        $this->request = $request;
        $this->_logger = $logger;
        $this->_infoHelper= $infoHelper;
        $this->printableColorFactory = $printableColorFactory;
        $this->printableColorCollection = $printableColorCollection;
        parent::__construct($context);
    }

    public function execute() {

        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $cacheKey = 'getColors';
            $response = $this->_infoHelper->loadCache($cacheKey);

            if (!$response) {
                $storeid = $this->_storeManager->getStore()->getId();
                $defaultColorID = $this->_helper->getConfig('productdesigner/customcolorpicker/color_picker_general', $storeid);
                $defaultColorData = $this->printableColorFactory->create()->load($defaultColorID)->getData();
                // echo "<pre>"; print_r($defaultColorData); exit;
                $allstore = array();
                array_push($allstore, 0);
                array_push($allstore, $storeid);
                $currentStore = $this->_storeManager->getStore();
                $path = $this->_dir->getRoot() . "/";
                $colorCollections = $this->printableColorCollection->create()
                ->addFieldToFilter('status', array('eq' => '1'));
                $color_data = [];
                $status = 0;
                foreach ($colorCollections as $colorCollection) {
                    $color_data[] = array(
                        'printablecolor_id' => $colorCollection->getData('printablecolor_id'),
                        'color_name' => $colorCollection->getData('color_name'),
                        'color_code' => $colorCollection->getData('color_code'),
                        'status' => $colorCollection->getData('status'),
                        'store_id' => $colorCollection->getData('store_id'),
                        'pantone_color' => $colorCollection->getData('pantone_color')
                    );
                    if($defaultColorID == $colorCollection['printablecolor_id']) {
                        $status = 1;                   
                    }
                }                
                if ($defaultColorData && $status==1) {
                    $dcolorData = array(
                        'printablecolor_id' => $defaultColorData['printablecolor_id'],
                        'color_code' => $defaultColorData['color_code'],
                    );
                } else {
                    $dcolorData = array(
                        'printablecolor_id' => $color_data[0]['printablecolor_id'],
                        'color_code' => $color_data[0]['color_code'],
                    );
                }
                $response['color'] = $color_data;
                $response['defaultColor'] = $dcolorData;
                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
