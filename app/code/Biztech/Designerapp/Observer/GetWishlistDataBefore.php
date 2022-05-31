<?php

namespace Biztech\Designerapp\Observer;

use Magento\Framework\Event\ObserverInterface;

class GetWishlistDataBefore implements ObserverInterface {

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
		$eventResponse = $observer->getData('productData');

		// get product details
		if(!$eventResponse) {
			return;
		}
		$productDetailData = $eventResponse->getProductData();

		// if byi url key exists, get byi url
		if(isset($productDetailData['byi_url'])) {
			// get design it enable for category listing page 
			$isDesignItCategoryEnable = $this->_dataHelper->isEnableCategory();

			$productDetailData['byi_url'] = $isDesignItCategoryEnable == true ? $this->_dataHelper->getByiUrl($productDetailData['id']) : false;
		}
		if(isset($productDetailData['byi_url_id'])) {
			$productDetailData['byi_url_id'] = $this->_dataHelper->isEnable($productDetailData['id']);
		}

		if(isset($productDetailData['is_addtocart'])){
			$productDetailData['is_addtocart'] = $this->_dataHelper->isAddToCart();
		}
		if(isset($productDetailData['image'])) {
			$image = $this->_dataHelper->setPreloadedTemplateImage($productDetailData['id']);
			// $productDetailData['image']	= ($image != '') ? $image : $productDetailData['image'];
			if($image != '') {
				$productDetailData['image'] = $image;
				$productDetailData['isPreloaded'] = true;
			} else {
				$productDetailData['isPreloaded'] = false;
			}
		}
		// 
		$eventResponse->setProductData($productDetailData);
	}
}