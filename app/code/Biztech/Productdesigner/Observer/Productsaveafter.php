<?php

namespace Biztech\Productdesigner\Observer;

use Magento\Framework\Event\ObserverInterface;

class Productsaveafter implements ObserverInterface
{    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();  
        if ($product->getTypeId() == 'configurable') {
	        $productTypeInstance = $product->getTypeInstance();
			$usedProducts = $productTypeInstance->getUsedProducts($product);
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        	if(!$product->getData('pre_loaded_template')){
				foreach ($usedProducts  as $child) {
				    $associatedProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($child->getId());
	                $associatedProduct->setPreLoadedTemplate('');
	                $associatedProduct->save();
				}
	        }else{
				foreach ($usedProducts  as $child) {
				    $associatedProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($child->getId());
	                $associatedProduct->setPreLoadedTemplate($product->getData('pre_loaded_template'));
	                $associatedProduct->save();
				}
	        }
        }
    }   
}