<?php

namespace Biztech\Designerapp\Observer;

use Magento\Framework\Event\ObserverInterface;

class GetOrderDetailDataBefore implements ObserverInterface {

	// Variable Declaration	
	protected $_designImageCollection;
	protected $_storeManager;
	protected $_productModel;
	protected $_designModel;
	protected $priceModel;
	protected $dataInterface;
	protected $_serialize;
	protected $attributeModel;
	
	// Dependancy Injector
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Biztech\Productdesigner\Model\Mysql4\Designimages\Collection $designImageCollection,
		\Magento\Catalog\Model\Product $productModel,
		\Biztech\Productdesigner\Model\Designs $designModel,
		\Magento\Catalog\Model\Product\Type\Price $priceModel,
		\Magento\Framework\App\ProductMetadataInterface $dataInterface,
		\Magento\Framework\Serialize\Serializer\Serialize $serialize,
		\Magento\Eav\Model\Entity\AttributeFactory $attributeModel
	) {
		$this->_storeManager = $storeManager;
		$this->_designImageCollection = $designImageCollection;
		$this->_productModel = $productModel;
		$this->_designModel = $designModel;
		$this->priceModel = $priceModel;
		$this->dataInterface = $dataInterface;
		$this->_serialize = $serialize;
		$this->attributeModel = $attributeModel;
	}

	// Init Function
	public function execute(\Magento\Framework\Event\Observer $observer) {

		// get event data
		$eventResponse = $observer->getData('productDetailData');

		// get order data
		$order = $observer->getData('orderData');
		$designId = null;
		$superAttributes = null;

		$additionalOptions = null;
		foreach($order->getAllVisibleItems() as $orderItem) {
			$productOptions = $orderItem->getData('product_options');
			if(isset($productOptions['info_buyRequest'])) {
				if(isset($productOptions['info_buyRequest']['super_attribute'])) {
					$superAttributes = $productOptions['info_buyRequest']['super_attribute'];
				}
			}
			if(isset($productOptions['additional_options'])) {
				$additionalOptions = $productOptions['additional_options'];
				break;
			}
		}
		if($additionalOptions) {                        
			foreach($additionalOptions as $additionalOption) {
				if(isset($additionalOption['design_id'])) {
					$designId = $additionalOption['design_id'];
					break;
				}
			}
		}
		// get product details
		$productDetailData = $eventResponse->getProductData();			

		if($designId != null) {
			$designImages  = $this->_designImageCollection->addFieldToFilter('design_id', Array('eq' => $designId))->addFieldToFilter('design_image_type', 'base')->getFirstItem()->getData();
			if (isset($designImages['image_path'])) {
				$path = $designImages['image_path'];
				$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
				$productDetailData[0]['image'] = $mediaUrl . "productdesigner/designs/" . $designId . "/base/" . $path;
			}
			$productDetailData[0]['unit_price'] = $this->setPrice($productDetailData, $designId, $superAttributes);
		}
		
		// 
		$eventResponse->setProductData($productDetailData);
	}

	public function setPrice($productDetailData, $designId, $superAttributes) {
		$itemProId = $productDetailData[0]['product_id'];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($itemProId);
		$designObj = $this->_designModel->load($designId);
		$prices = json_decode(base64_decode($designObj->getPrices()), true);
		$prices = $prices['objPrices'];

		if ($product->getTypeId() == 'configurable') {
			$attrs = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

			$configurable_attributes = array();
			foreach ($attrs as $attr) {
				$configurable_attributes[] = $attr['attribute_code'];
			}

			$attrLen = count($configurable_attributes);
			$productTypeInstance = $product->getTypeInstance();
			$simpleproduct = '';
			$simpleCollection = $productTypeInstance->getUsedProductCollection($product)
			->addAttributeToSelect('*');

			for ($i = 0; $i < $attrLen; $i++) {
				$designdata1 = $superAttributes;
				$attrid = $this->attributeModel->create()->loadByCode('catalog_product', $configurable_attributes[$i])->getAttributeId();
				$attr = $designdata1[$attrid];
				$simpleCollection->addAttributeToFilter($configurable_attributes[$i], $attr);
			}

			foreach ($simpleCollection as $simple) {
				$simpleproduct = $simple;
				break;
			}
			$base_price = $this->priceModel->getBasePrice($simpleproduct, $productDetailData[0]['ordered_qty']);
		} else {
			$base_price = $this->priceModel->getBasePrice($product, $productDetailData[0]['ordered_qty']);
		}
		
		$custom_price = $base_price + $prices;
		$custom_price = ($custom_price < 0) ? 0 : $custom_price;
		return $custom_price;

	}

	protected function serializeData($value) {
		$string = '';
		if (version_compare($this->dataInterface->getVersion(), '2.2.0', '>=')) {
			$string = json_encode($value);
		} else {
            // $string = serialize($value);
			$string = $this->_serialize->serialize($value);
		}
		return $string;
	}

	public function unserializeData($value) {
		$string = '';
		if (version_compare($this->dataInterface->getVersion(), '2.2.0', '>=')) {
			$string = json_decode($value, true);
		} else {
			$string = (isset($value) && $value) ? $this->_serialize->unserialize($value) : '';
            // $string = unserialize($value);
		}
		return $string;
	}
}