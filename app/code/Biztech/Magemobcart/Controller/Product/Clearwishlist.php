<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Clearwishlist extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $wishlistModel;
    protected $formKey;
        
    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session     $customerSession
     * @param \Magento\Wishlist\Model\Wishlist    $wishlistModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\Wishlist $wishlistModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_wishlistModel = $wishlistModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for clear all wishlist items
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
            $postData = $this->_request->getParams();
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            try {
                $productDetailResult = array();
                $customerId = $postData['customer_id'];
                if (isset($customerId) && $customerId != null) {
                    $deleted = false;
                    $wishlistModelData = $this->_wishlistModel->loadByCustomerId($customerId);
                    $wishlistItems = $wishlistModelData->getItemCollection();

                    foreach ($wishlistItems as $item) {
                        $item->delete();
                        $wishlistModelData->save();
                        $deleted = true;
                    }
                    if ($deleted == true) {
                        $productDetailResult = array(
                                'status' => 'true',
                                'message' => 'Products has been removed from your wishlist.'
                            );
                    } else {
                        $productDetailResult = array(
                                'status' => 'false',
                                'message' => 'Products doesnot exist in your wishlist.'
                            );
                    }
                } else {
                    $productDetailResult = array(
                        'status' => 'false',
                        'message' => 'Please login to add/remove product to wishlist'
                    );
                }
            } catch (\Exception $e) {
                $productDetailResult = array(
                    'status' => 'error',
                    'message' => $e->getMessage()
                );
            }
            $jsonResult->setData($productDetailResult);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
