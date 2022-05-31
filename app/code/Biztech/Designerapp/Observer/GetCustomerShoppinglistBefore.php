<?php

namespace Biztech\Designerapp\Observer;

use Magento\Framework\Event\ObserverInterface;

class GetCustomerShoppinglistBefore implements ObserverInterface {

	// Variable Declaration	
	protected $_designImageCollection;
	protected $_storeManager;
	
	// Dependancy Injector
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Biztech\Productdesigner\Model\Mysql4\Designimages\Collection $designImageCollection
	) {
		$this->_storeManager = $storeManager;
		$this->_designImageCollection = $designImageCollection;
	}

	// Init Function
	public function execute(\Magento\Framework\Event\Observer $observer) {

		// get event data
		$eventResponse = $observer->getData('productImage');

		$pimg = $eventResponse->getProductImage();		
		$designId = $observer->getData('designId');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$designImages = $objectManager->get('Biztech\Productdesigner\Model\Designimages')->getCollection()->addFieldToFilter('design_id', Array('eq' => $designId))->addFieldToFilter('design_image_type', 'base')->getFirstItem()->getData();
		// $designImages  = $this->_designImageCollection->addFieldToFilter('design_id', Array('eq' => $designId))->addFieldToFilter('design_image_type', 'base')->getFirstItem()->getData();


		if (isset($designImages['image_path'])) {
			$path = $designImages['image_path'];
			$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			$pimg = $mediaUrl . "productdesigner/designs/" . $designId . "/base/" . $path;
		}
		
		$eventResponse->setProductImage($pimg);
		$designId = "";
	}
}