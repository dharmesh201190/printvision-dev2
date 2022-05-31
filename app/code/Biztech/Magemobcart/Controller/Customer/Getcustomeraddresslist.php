<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Getcustomeraddresslist extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $customerModel;
    protected $countryModel;
    protected $formKey;
    
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Directory\Model\Country $countryModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_customerModel = $customerModel;
        $this->_countryModel = $countryModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all available customer address list.
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
                $billingAddressDetail = array();
                
                if (array_key_exists('customer_id', $postData)) {
                    $customerId = $postData['customer_id'];
                    if ($customerId != "") {
                        $customerData = $this->_customerModel->load($customerId);
                        $basicDetail = array(
                        'entity_id' => $customerData->getEntityId(),
                        'firstname' => $customerData->getFirstname(),
                        'lastname' => $customerData->getLastname(),
                        'email' => $customerData->getEmail(),
                        );
                        foreach ($customerData->getAddresses() as $address) {
                            $billingType = 0;
                            $shippingType = 0;
                            $billingCountryName = null;

                            if ($address->getCountryId()) {
                                $billingCountryName = $this->_countryModel->loadByCode($address->getCountryId())->getName();
                            }

                            if ($address->getId() == $customerData->getDefaultBilling()) {
                                $billingType = 1;
                            }

                            if ($address->getId() == $customerData->getDefaultShipping()) {
                                $shippingType = 1;
                            }

                            $billingAddressDetail[] = array(
                            'firstname' => $address->getFirstname(),
                            'lastname' => $address->getLastname(),
                            'street' => $address->getData('street'),
                            'city' => $address->getCity(),
                            'region_id' => $address->getRegionId() ? (string)$address->getRegionId() : '',
                            'region' => $address->getRegion(),
                            'postcode' => $address->getPostcode(),
                            'country' => $billingCountryName,
                            'country_id' => $address->getCountryId(),
                            'telephone' => $address->getTelephone(),
                            'address_id' => $address->getId(),
                            'billing_type' => $billingType,
                            'shipping_type' => $shippingType
                            );
                        }
                        $customer_detail = array(
                        'basic_details' => $basicDetail,
                        'address_list' => $billingAddressDetail,
                        );
                        $jsonResult->setData($customer_detail);
                        return $jsonResult;
                    } else {
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = 'Customer id should not blank';
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    }
                } else {
                    $responseArr['status'] = 'false';
                    $responseArr['message'] = 'Customer id is missing';
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
