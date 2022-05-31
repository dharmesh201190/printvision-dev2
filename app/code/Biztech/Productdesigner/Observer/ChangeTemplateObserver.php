<?php


namespace Biztech\Productdesigner\Observer;

use Magento\Framework\Event\ObserverInterface;

class ChangeTemplateObserver implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $observer->getBlock()->setTemplate('Biztech_Productdesigner::helper/gallery.phtml');
    }

}
