<?php

namespace Biztech\Designerapp\Observer;

use Magento\Framework\Event\ObserverInterface;

class GetCartDataBefore implements ObserverInterface {

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
		$eventResponse = $observer->getData('cartResponse');

		// get product details
		if(!$eventResponse) {
			return;
		}
		$cartDetailData = $eventResponse->getCartData();

		foreach ($cartDetailData['items'] as $key => $cartItem) {

			// if byi url key exists, get byi url
			if(isset($cartItem['byi_url'])) {
				$cartDetailData['items'][$key]['byi_url'] = $this->_dataHelper->getByiUrl($cartItem['product_id'], true);
				$cartDetailData['items'][$key]['byi_url'] .= '/design/' . $cartItem['designId'];
				$cartDetailData['items'][$key]['byi_url'] .= '/item/' . $cartItem['itemId'];
				$cartDetailData['items'][$key]['byi_url'] .= '/isApp/true';
			}
			if(isset($cartItem['byi_url_product_id'])) {
				$cartDetailData['items'][$key]['byi_url_id'] = $this->_dataHelper->isEnable($cartItem['product_id']);
			}			
		}

		// 
		$eventResponse->setCartData($cartDetailData);
	}
}