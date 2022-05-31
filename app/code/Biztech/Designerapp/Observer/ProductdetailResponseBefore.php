<?php

namespace Biztech\Designerapp\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductdetailResponseBefore implements ObserverInterface {

	// Variable Declaration
	protected $_dataHelper;	
	
	// Dependancy Injector
	public function __construct(
		\Biztech\Designerapp\Helper\Data $dataHelper
	) {
		$this->_dataHelper = $dataHelper;	
	}

	// Init Function
	public function execute(\Magento\Framework\Event\Observer $observer) {

		// get event data
		$eventResponse = $observer->getData('productDetailData');

		// get product details
		$productDetailData = $eventResponse->getProductData();
		
		// iterate each data one by one
		foreach ($productDetailData as $parentKey=>$resp) {

			// if byi url key exists, get byi url
			if(isset($resp['byi_url'])) {
				// get design it enable for category listing page 
				$isDesignItCategoryEnable = $this->_dataHelper->isEnableCategory();

				$productDetailData[$parentKey]['byi_url'] = $isDesignItCategoryEnable == true ? $this->_dataHelper->getByiUrl($resp['id']) : false;
			}
			if(isset($resp['byi_url_id'])) {
				$productDetailData[$parentKey]['byi_url_id'] = $this->_dataHelper->isEnable($resp['id']);
			}
			if(isset($resp['is_addtocart'])){
				$productDetailData[$parentKey]['is_addtocart'] = $this->_dataHelper->isAddToCart();
			}
			if(isset($resp['image'])) {
				$image = $this->_dataHelper->setPreloadedTemplateImage($resp['id']);
				// $productDetailData[$parentKey]['image']	= ($image != '') ? $image : $productDetailData[$parentKey]['image'];
				if($image != '') {
					$productDetailData[$parentKey]['image'] = $image;
					$productDetailData[$parentKey]['isPreloaded'] = true;
				} else {
					$productDetailData[$parentKey]['isPreloaded'] = false;
				}
			}
		}
		$eventResponse->setProductData($productDetailData);
	}
}