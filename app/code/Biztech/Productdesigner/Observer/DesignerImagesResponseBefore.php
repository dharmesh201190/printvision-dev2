<?php

namespace Biztech\Productdesigner\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Checkout\Model\Session as CheckoutSession;

class DesignerImagesResponseBefore implements ObserverInterface {

	protected $_imageSideModel;

	public function __construct(
		\Biztech\Productdesigner\Model\Side $imageSideModel
	) {
		$this->_imageSideModel = $imageSideModel;
	}

    public function execute(\Magento\Framework\Event\Observer $observer) {
    	$eventResponse = $observer->getData('designerItem');
    	$designerItem = $eventResponse->getDesignerItem();
    	if($designerItem && count($designerItem) > 0 && isset($designerItem['image_side'])) {
    		$imageSideId = $designerItem['image_side'];
    		$imageSideData = $this->_imageSideModel->load($imageSideId);
    		if($imageSideData && isset($imageSideData['sort_order'])) {
    			$designerItem['sort_order'] = $imageSideData['sort_order'];
    			$eventResponse->setDesignerItem($designerItem);
    		}
    	}
    }
}