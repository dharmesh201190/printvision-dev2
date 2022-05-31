<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Addcustomeraddress extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $customerAddressModel;
    protected $formKey;
    
    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session     $customerSession
     * @param \Magento\Customer\Model\Address     $customerAddressModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Address $customerAddressModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_customerAddressModel = $customerAddressModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used add customer billing and shipping address
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
                $customer_address_array = array();
                $address = $this->_customerAddressModel;
                $address->setData($postData)
                        ->setCustomerId($postData['customer_id'])
                        ->setIsDefaultBilling($postData['default_billing'])
                        ->setIsDefaultShipping($postData['default_shipping'])
                        ->setSaveInAddressBook('1');
                $address->save();

                $customer_address_array = array(
                    'address_id' => $address->getId(),
                    'status' => 'success',
                    'message' => 'Address Added Successfully'
                );
                $jsonResult->setData($customer_address_array);
                return $jsonResult;
            } catch (\Exception $e) {
                $returnExtensionArray = array('error' => $e->getMessage());
                $jsonResult->setData($returnExtensionArray);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
