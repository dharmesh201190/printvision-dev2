<?php

namespace Biztech\Designerapp\Observer;

use Magento\Framework\Event\ObserverInterface;

class GetStoreDetailBefore implements ObserverInterface {

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
		$eventResponse = $observer->getData('storeResponse');

		// get product details
		$storeResponse = $eventResponse->getStoreResponse();
		
			// if byi url key exists, get byi url			
		if(isset($storeResponse['byi_enabled'])) {
			$storeResponse['byi_enabled'] = $this->_dataHelper->isEnableByStore();
		}
		
		$eventResponse->setStoreResponse($storeResponse);
	}
}