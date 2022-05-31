<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class GetMyDesigns extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $countryModel;
    protected $_objectFactory;

    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Customer\Model\Session     $customerSession
     * @param \Magento\Directory\Model\Country    $countryModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Directory\Model\Country $countryModel,
        \Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_countryModel = $countryModel;
        $this->_objectFactory = $objectFactory;
        parent::__construct($context);
    }

    /**
     * This function is used for get all available countries list.
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        if ($this->_cartHelper->isEnable()) {
            if (!$this->_cartHelper->getHeaders()) {
                $errorResult = array('status'=> false,'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $sessionId = '';
            $postData = $this->_request->getParams();
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $session = $objectManager->get('Magento\Customer\Model\Session');
            $currentStoreId = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            $customerData = $session->getCustomer();
            $customer_id = $customerData->getId();
            $customer_id = $postData['customer_id'];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $obj_product = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Designs\Collection')->addFieldToFilter('customer_id', array('eq' => $customer_id));
            $obj_product->getSelect()->order('design_id DESC');
            $collections = $obj_product->getData();
            // echo "<pre>";
            // print_r($collections);
            // die();
            $demo = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
            foreach ($collections as $mydesign) {
                $design_id = $mydesign['design_id'];
                $obj_product = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Designimages\Collection')->addFieldToFilter('design_id', array('eq' => $design_id))->addFieldToFilter('design_image_type', 'base');
                $designImages = $obj_product->getData();

                $designForProduct = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Designs\Collection')->addFieldToFilter('design_id', array('eq' => $design_id))->getData();
                $dateCreated = $designForProduct[0]['created_at'];
                $customer_id = $designForProduct[0]['customer_id'];
                if ($customer_id) {
                    $userPath = $customer_id;
                } else {
                    $userPath = 'guest';
                }
                $path = $demo->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "productdesigner/designs/" . $design_id . "/base/";
                $obj_product = $objectManager->create('Magento\Catalog\Model\Product');
                $product = $obj_product->load($mydesign['product_id']);
                $prod_name = $product->getName();
                $status = $product->getStatus();
                if ($status == 1) {
                    if (isset($designImages[0]['image_path'])) {
                        $byi_url = $demo->getStore()->getBaseUrl() . 'productdesigner/index/index/id/' . $product->getId() . '/design/' . base64_encode($design_id) . '/isApp/true';
                        $finalArray[] = array(
                            'id' => $product->getId(),
                            // 'id' => '2047',
                            'sku' => $product->getSku(),
                            'name' => $prod_name,
                            'status' => $status,
                            'qty' => 100,
                            'in_stock' => 1,
                            'price' => $product->getFinalPrice(),
                            'special_price' => $product->getSpecialPrice(),
                            'image' => $path . $designImages[0]['image_path'],
                            'type' => $product->getTypeId(),
                            'is_wishlisted' => false,
                            'wishlist_item_id' => null,
                            'review_count' => 0,
                            'average_rating' => null,
                            'byi_url_id' => 1234556,
                            'byi_url' => $byi_url,
                            'design_id' => $design_id,
                            'path' => $path . $designImages[0]['image_path'],
                            'edit_url' => $byi_url,
                            'has_options' => 1,
                            'save_discount' => null,
                            'delete' => $demo->getStore()->getBaseUrl() .'productdesigner/index/deleteDesign/'.$design_id
                        );
                    }
                }
            }
            // $customObject = $this->_objectFactory->create();
            // $customObject->setProductData($finalArray);
            // $this->_eventManager->dispatch('productdetail_response_before', ['productDetailData' => $customObject]);
            // $finalArray = $customObject->getProductData();
            $jsonResult->setData(array('productCollection'=>$finalArray));
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
