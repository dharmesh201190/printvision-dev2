<?php

namespace Biztech\Productdesigner\Controller\Cart;

class Save extends \Biztech\Productdesigner\Controller\Index {

    public function execute() {
        try {

            // get payload from params
            $payload = json_decode(file_get_contents('php://input'), TRUE);
            // init response obj
            $response = array();

            // init item id
            $itemId = '';

            // if payload does not contain product id, set error
            if (!isset($payload['productId'])) {
                $this->throwError();
            }

            // if item id is passed, that means we need to delete previous item first from cart
            if (isset($payload['itemId'])) {
                $itemId = base64_decode($payload['itemId']);
                $this->removeItemFromCart($itemId);
            }

            // get product id from payload
            $productId = $payload['productId'];

            // get current product's object
            $_product = $this->_productLoader->create()->load($productId);

            // get product type
            $productType = $_product->getTypeId();

            $customCart = $this->cartModel->create();

            // if product is simple product
            if ($productType == "simple") {
                $response = $this->saveSimpleProduct($payload, $customCart);
            }

            // if product is configurable product
            else if ($productType == "configurable") {
                $response = $this->saveConfigurableProduct($payload, $customCart);
            }
            // set response
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $response['message'] = $e->getMessage();
            $this->getResponse()->setBody(json_encode($response));
        }
    }
    protected function removeItemFromCart($itemId) {
        $quoteItem = $this->getItemModel()->load($itemId);
        $quoteItem->delete();
    }

    protected function saveSimpleProduct($payload, $customCart) {
        // prepare params

        $params = array(
            'qty' => $payload['qty'],
            'product_id' => $payload['productId'],
            'options' => array('design' => (!empty($payload['designId'])) ? $payload['designId'][0] : 0)
        );
        $this->getRequest()->setPostValue('designId', array(0 => $payload['designId'][0]));
        // save cart data
        $this->addToCart($params, $payload, $customCart);

        // check if add product to cart was successful 
        $response = $this->saveCartData($customCart);

        // 
        return $response;
    }

    protected function saveConfigurableProduct($payload, $customCart) {
        $count = 0;
        // prepare cart data for configurable attributes and save it one by one
        foreach ($payload['confProductData'] as $confProductData) {

            // prepare cart data
            $params = array(
                'product_id' => $payload['productId'],
                'qty' => $confProductData['qty'],
                'super_attribute' => $confProductData['super_attribute'],
                'options' => array('design' => (!empty($payload['designId'])) ? $payload['designId'][$count] : 0)
            );
            $this->getRequest()->setPostValue('designId', array(0 => $payload['designId'][$count]));
            // save cart data
            $response = $this->addToCart($params, $payload, $customCart);
            $count++;
        }
        // check if add product to cart was successful 
        $response = $this->saveCartData($customCart);

        // 
        return $response;        
    }
    protected function addToCart($params, $payload, $customCart) {
        // get current product's object
        $_product = $this->_productLoader->create()->load($payload['productId']);

        // if custom options is set
        if (isset($payload['customOptions'])) {
            $params['options'] = $payload['customOptions'];
        }

        // add product into cart with params
        $customCart->addProduct($_product, $params);

    }        

    protected function throwError() {
        $response = array();
        $response['status'] = 'failure';
        $response['log'] = "No Product id defined";
        $this->getResponse()->setBody(json_encode($response));
    }

    protected function saveCartData($customCart) {
        if (isset($customCart)) {            

            // save cart data
            $customCart->save();

            // set flag
            $customCart->setCartWasUpdated(true);

            // prepare response
            $response['url'] = $this->_url->getUrl('checkout/cart');
            $response['status'] = 'success';
            $response['message'] = 'Product is added to shopping cart';
            $response['status'] = "success";

            // set success message
            $this->messageManager->addSuccess(__('Add to cart successfully.'));
        }

        // else prepare error for the same
        else {
            $response['status'] = "failure";
            $response['log'] = "Could not save!";
        }

        // return response
        return $response;
    }

    public function getItemModel() {
        $itemModel = $this->quoteItemFactory->create();
        return $itemModel;
    }

}
