<?php

namespace Biztech\Productdesigner\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Checkout\Model\Session as CheckoutSession;

class catalogProductLoadAfter implements ObserverInterface {

    protected $_request;
    protected $_eavAttributeModel;
    protected $designFactory;
    protected $attributeModel;
    protected $priceModel;
    protected $dataInterface;
    protected $_serialize;
    private $request;
    private $registry;
    protected $_objectManager;
    protected $salesOrderItems;
    private $_checkoutSession;
    protected $session;


    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttributeModel,
        \Biztech\Productdesigner\Model\DesignsFactory $designFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeModel,
        \Magento\Catalog\Model\Product\Type\Price $priceModel,
        \Magento\Framework\App\ProductMetadataInterface $dataInterface,
        Serialize $serialize,
        \Magento\Framework\App\RequestInterface $requesta,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $salesOrderItemsFactory,
        CheckoutSession $checkoutSession,
        \Magento\Framework\Session\SessionManagerInterface $session
    ) {
        $this->_request = $request;
        $this->_eavAttributeModel = $eavAttributeModel;
        $this->designFactory = $designFactory;
        $this->attributeModel = $attributeModel;
        $this->priceModel = $priceModel;
        $this->dataInterface = $dataInterface;
        $this->_serialize = $serialize;
        $this->request = $requesta;
        $this->registry = $registry;
        $this->_objectManager = $objectManager;
        $this->salesOrderItems = $salesOrderItemsFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->session = $session;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
    	$this->session->start();
        $item = $observer->getQuoteItem();
        $item->setLastAdded(true);
        $data = json_decode(file_get_contents('php://input'), TRUE);
        $action = $this->_request->getFullActionName();
        if ($action == 'productdesigner_Cart_Save') {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            if (isset($data['customOptionFile'])) {
                foreach ($data['customOptionFile'] as $customOptionFile) {
                    $optionId = $customOptionFile['optionId'];
                    $fileName = $customOptionFile['fileName'];
                    $filePath = $customOptionFile['filePath'];
                    $optiontitle = $customOptionFile['optiontitle'];
                    $additionalOptions[] = array(
                        'code' => 'file-' . $optionId,
                        'label' => $optiontitle,
                        'value' => '<a href="' . $filePath . '" target="_blank">' . $fileName . '</a>',
                    );
                }
            }


            if (isset($data)) {
                $post = $this->_request->getPostValue();
                $designId = $post['designId'][0];
                $additionalOptions[] = array(
                    'code' => 'product_design',
                    'label' => __('Product Design'),
                    'design_id' => $designId,
                    'value' => __('Yes'),
                    'custom_view' => false,
                );
            }
            
            $optionData = array(
                'product_id' => $data['productId'],
                'code' => 'additional_options',
                'label' => 'Product Design',
                'value' => $this->serializeData($additionalOptions),
            );
            $designObj = $this->designFactory->create()->load($designId);

            // calc price
            $item = $observer->getQuoteItem();
            $item->addOption($optionData);
            $item = $observer->getEvent()->getData('quote_item');
            $product = $observer->getEvent()->getData('product');
            $prices = json_decode(base64_decode($designObj->getPrices()), true);
            $prices = $prices['objPrices'];

            $itemProId = $item->getProduct()->getId();
            if ($item->getProduct()->getTypeId() == 'configurable') {
                $attrs = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

                $configurable_attributes = array();
                foreach ($attrs as $attr) {
                    $configurable_attributes[] = $attr['attribute_code'];
                }

                $attrLen = count($configurable_attributes);
                $params = $item->getProduct()->getCustomOptions();
                $productTypeInstance = $product->getTypeInstance();
                $simpleproduct = '';
                $simpleCollection = $productTypeInstance->getUsedProductCollection($product)
                ->addAttributeToSelect('*');

                for ($i = 0; $i < $attrLen; $i++) {
                    $designData = $params['attributes']->getData();
                    $designdata1 = $this->unserializeData($designData['value']);
                    $attrid = $this->attributeModel->create()->loadByCode('catalog_product', $configurable_attributes[$i])->getAttributeId();
                    $attr = $designdata1[$attrid];
                    $simpleCollection->addAttributeToFilter($configurable_attributes[$i], $attr);
                }

                foreach ($simpleCollection as $simple) {
                    $simpleproduct = $simple;
                    break;
                }
                $base_price = $this->priceModel->getBasePrice($simpleproduct, $item->getQty());
            } else {
                $base_price = $this->priceModel->getBasePrice($item->getProduct(), $item->getQty());
            }

            if (isset($data['additionalPrice']) && $data['additionalPrice'] > 0) {
                $prices += $data['additionalPrice'];
            }

            if (isset($data['customOptionPrice'])) {
                $prices += $data['customOptionPrice'];
            }
            $custom_price = $base_price + $prices;
            $custom_price = ($custom_price < 0) ? 0 : $custom_price;
            $item->setCustomPrice($custom_price);
            $item->setOriginalCustomPrice($custom_price);
            $item->getProduct()->setIsSuperMode(true);
        } elseif ($action == 'sales_order_reorder') {
          
            $order = $this->registry->registry('current_order');
            $order_id = $order->getId();
            $designId = "";
            $productId = $item->getBuyRequest()->getData('product_id');
                                  
       	    foreach ($order->getAllItems() as $key => $items) {

                $additionaloptions = $items->getProductOptionByCode('additional_options');
                $product_id = $items->getBuyRequest()->getData('product_id');
                if($additionaloptions){
            	   
                    foreach ($additionaloptions as $itemkey => $itemvalue) {
                        if($itemkey == 0){                        
                            $designId = $itemvalue['design_id'];
                        }
                    }

                    $buyInfo['design'] = $designId;
                    if (isset($buyInfo['design']) && $buyInfo['design'] && $this->session->getItemDesignId() == $buyInfo['design']) {
                        $item = $observer->getEvent()->getData('quote_item');
                        $product = $observer->getEvent()->getData('product');
                        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
                        $designModel = $this->_objectManager->create('Biztech\Productdesigner\Model\Mysql4\Designs\Collection')
                        ->addFieldToFilter('design_id', array('eq' => $buyInfo['design']))
                        ->getFirstItem()->getData();
                        $additionalOptions = array();
                        $additionalOptions[] = array(
                            'product_id' => $product_id,
                            'code' => 'product_design',
                            'label' => 'Product Design',
                            'design_id' => $buyInfo['design'],
                            'value' => 'Yes',
                            'custom_view' => false,
                        );
                        $item->addOption(
                            array(
                                'product_id' => $product_id,
                                'code' => 'additional_options',
                                'label' => 'Product Design',
                                'value' => $this->serializeData($additionalOptions),
                            )
                        );

                        /*
                         * Added By BC : For names and numbers custom options
                        */
                        
                        if ($product->getTypeId() == 'configurable') {
                            $attrs = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                            $default_attributes = $product->getDefaultAttributeCode();
                            $configurable_attributes = array();
                            foreach ($attrs as $attr) {
                                $configurable_attributes[] = $attr['attribute_code'];
                            }
                            if (($key = array_search($default_attributes, $configurable_attributes)) !== false) {
                                unset($configurable_attributes[$key]);
                            }
                            $configurable_attributes = array_values($configurable_attributes);
                            $attrLen = count($configurable_attributes);

                            $nameNumberData = json_decode($designModel['name_number_details'], true);
                            
                        	if ($nameNumberData && count($nameNumberData) > 0) {
                        	    $namesAndnumbersString = array();
                        	    $namesAndnumbers = '';
                        	    $nameNumberArr = array();
                        	    foreach ($nameNumberData as $nameNumberObj) {
                        	        $nameNumberArr = array();
                        	        foreach ($nameNumberObj as $key => $value) {
                        	            if ($key != 'id' && $key != 'isGenerated') {
                        	                if (in_array(ucwords($key), $namesAndnumbersString, true) == false) {
                        	                    if (count($namesAndnumbersString) != 3)
                        	                        array_push($namesAndnumbersString, ucwords($key));
                        	                }
                        	                array_push($nameNumberArr, $value);
                        	            }
                        	        }
                        	        $namesAndnumbers .= implode(" / ", $nameNumberArr);
                        	        $namesAndnumbers .= "<br>";
                        	    }
                        	    $namesAndnumbersString = implode(" / ", $namesAndnumbersString);
                        	    if ($additionalOption = $item->getOptionByCode('additional_options')) {
                        	        $additionalOptions = json_decode($additionalOption->getValue(), TRUE);
                        	    }
                        	    $additionalOptions[] = array(
                        	        'product_id' => $product_id,
                        	        'code' => 'name_numbers',
                        	        'label' => $namesAndnumbersString,
                        	        'design_id' => $designId,
                        	        'value' => $namesAndnumbers,
                        	        'custom_view' => false
                        	    );

                        	    $optionData = array(
                        	        'product_id' => $product_id,
                        	        'code' => 'additional_options',
                        	        'label' => 'Product Design',
                        	        'value' => $this->serializeData($additionalOptions)
                        	    );

                        	    $item->addOption($optionData);
                        	}

                        }
                        /*
                         * END: For names and numbers custom options
                        */


                        /*
                         * Added By BC : For Printing Method
                        */
                            if(!empty($designModel['printing_method_details'])){
                                $printingMethod_data = json_decode($designModel['printing_method_details'], true);

                                if ($additionalOption = $item->getOptionByCode('additional_options')) {
                                    $additionalOptions = json_decode($additionalOption->getValue(), TRUE);
                                }
                                
                                $additionalOptions[] = array(
                                    'product_id' => $product_id,
                                    'code' => 'printing_method',
                                    'label' => 'Printing Method',
                                    'value' => $printingMethod_data['printing_method'],
                                    'custom_view' => false
                                );
                                $additionalOptions[] = array(
                                    'product_id' => $product_id,
                                    'code' => 'printing_surcharge',
                                    'label' => 'Printing Price',
                                    'value' => $printingMethod_data['printing_surcharge'],
                                    'custom_view' => false,
                                );
                                $item->addOption(
                                        array(
                                            'product_id' => $product_id,
                                            'code' => 'additional_options',
                                            'label' => 'Product Design',
                                            'value' => $this->serializeData($additionalOptions)
                                        )
                                );
                                
                            }
                        /*
                         * END: For Printing Method
                        */


                        
                        $data = json_decode(base64_decode($designModel['prices']), true);
                        $prices = $data['objPrices'];
                        
                        if ($item->getProduct()->getTypeId() == 'configurable') {
                            $attrs = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

                            $configurable_attributes = array();
                            foreach ($attrs as $attr) {
                                $configurable_attributes[] = $attr['attribute_code'];
                            }

                            $attrLen = count($configurable_attributes);

                            $params = $item->getProduct()->getCustomOptions();

                            $productTypeInstance = $product->getTypeInstance();
                            $simpleproduct = '';
                            $simpleCollection = $productTypeInstance->getUsedProductCollection($product)
                            ->addAttributeToSelect('*');

                            
                            for($i = 0; $i < $attrLen; $i++) {
                                $designData = $params['attributes']->getData();
                                $designdata1 = $this->unserializeData($designData['value']);
                                $attrid = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute')->loadByCode('catalog_product', $configurable_attributes[$i])->getAttributeId();
                                $attr = $designdata1[$attrid];
                                $simpleCollection->addAttributeToFilter($configurable_attributes[$i], $attr);
                            }
                            foreach ($simpleCollection as $simple) {
                                $simpleproduct = $simple;
                                break;
                            }
                            $price_object = $this->_objectManager->get('Magento\Catalog\Model\Product\Type\Price');
                            $base_price = $price_object->getBasePrice($simpleproduct, $item->getQty());

                            if (isset($data['additional_price']) && $data['additional_price'] > 0) {
                                $prices += $data['additional_price'];
                            }

                            if (isset($data['customOptionsPrice']) && $data['customOptionsPrice'] > 0)
                            {
                                $prices += $data['customOptionsPrice'];
                            }                                

                            $custom_price = $base_price + $prices;
                            $custom_price = ($custom_price < 0) ? 0 : $custom_price;

                        } else {
                            $price_object = $this->_objectManager->get('Magento\Catalog\Model\Product\Type\Price');
                            $base_price = $price_object->getBasePrice($item->getProduct(), $item->getQty());
                          
                            if (isset($data['additional_price']) && $data['additional_price'] > 0) {
                                $prices += $data['additional_price'];
                            }

                            if (isset($data['customOptionsPrice']) && $data['customOptionsPrice'] > 0)
                            {
                                $prices += $data['customOptionsPrice'];
                            }

                            $custom_price = $base_price + $prices;
                            $custom_price = ($custom_price < 0) ? 0 : $custom_price;

                        }
                        $item->setCustomPrice($custom_price);
                        $item->setOriginalCustomPrice($custom_price);
                        $item->getProduct()->setIsSuperMode(true);
                    }
                    
                }
                
            }
        }
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
