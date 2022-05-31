<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class GetCustomerwishlist extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $formKey;
    
    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session     $customerSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all wishlist product list of customer.
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
            try {
                $postData = $this->_request->getParams();
                $sessionId = '';
                if (isset($postData['session_id']) && $postData['session_id'] != null) {
                    $sessionId = $postData['session_id'];
                    if (!$this->_customerSession->isLoggedIn()) {
                        $customerId = explode("_", $sessionId);
                        $this->_cartHelper->relogin($customerId[0]);
                    }
                }
                if (array_key_exists('customer_id', $postData) && array_key_exists('storeid', $postData)) {
                    $customerId = $postData['customer_id'];
                    $storeId = $postData['storeid'];
                    if ($customerId != "" && $storeId != "") {
                        $wishlistCollection = array();
                        $wishlistCollection = $this->_cartHelper->getWishlistData($customerId, $storeId);
                        $responseArr['wishlistCollection'] = $wishlistCollection;
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    } else {
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = 'Customer and Store should not blank';
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    }
                } else {
                    $responseArr['status'] = 'false';
                    $responseArr['message'] = 'Store or customer is missing';
                    $jsonResult->setData($responseArr);
                    return $jsonResult;
                }
            } catch (\Exception $e) {
                $responseArr['status'] = 'false';
                $responseArr['message'] = $e->getMessage();
                $jsonResult->setData($responseArr);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
