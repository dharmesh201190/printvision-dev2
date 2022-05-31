<?php

namespace Biztech\Productdesigner\Controller\Index;

class saveCroppedImage extends \Magento\Framework\App\Action\Action {

	protected $_infoHelper;
    protected $_filesystem;
    protected $_storeManager;
   
    public function __construct(
    	\Magento\Framework\App\Action\Context $context,
    	\Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Framework\Filesystem $filesystem, \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    	$this->_infoHelper = $infoHelper;
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
    	parent::__construct($context);
    }

    public function execute() {

    	try {
    		$postData = json_decode(file_get_contents('php://input'), TRUE);
    		$imageData = explode(",", $postData['imageData']);
    		$imageData = base64_decode($imageData[1]);
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    		$path = $reader->getAbsolutePath(). 'productdesigner/cropped/' ;
    		$imageName = time() . rand() . '.png';

    		if (!file_exists($path)) {
    			mkdir($path, 0777, true);
    		}
    		file_put_contents($path . $imageName, $imageData);
            $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    		$output = $url . 'productdesigner/cropped/'. $imageName;
    		$this->getResponse()->setBody(json_encode(array('outputImage' => $output)));
    	} catch (\Exception $e) {
    		$response = $this->_infoHelper->throwException($e, self::class);
    		$this->getResponse()->setBody(json_encode($response));
    	}
    }

}
