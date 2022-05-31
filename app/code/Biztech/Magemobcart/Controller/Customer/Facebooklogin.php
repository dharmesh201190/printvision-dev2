<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Facebooklogin extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $newsletterModel;
    protected $cartModel;
    protected $formKey;

    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Customer\Model\Session     $customerSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\Subscriber $newsletterModel,
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_newsletterModel = $newsletterModel;
        $this->_cartModel = $cartModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }
    /**
     * This function is used for login with facebook details.
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
            $responseArr['status'] = 'false';
            $storeId = $postData['storeid'];
            $website_id = $postData['website_id'];
            $email = $postData['email'];
            $responseArr['status'] = 'false';
            if ($email) {
                $customer = $this->_cartHelper->getCustomerByEmail($email, $website_id);
                
                if (!$customer || !$customer->getId()) {
                    $customer = $this->_cartHelper->createCustomerMultiWebsite($postData, $website_id, $storeId);
                    // $customer->sendPasswordReminderEmail();
                }
                if ($customer->getConfirmation()) {
                    try {
                        $customer->setConfirmation(null);
                        $customer->save();
                    } catch (\Exception $e) {
                    }
                }
                $this->_customerSession->setCustomerAsLoggedIn($customer);
                
                if ($this->_customerSession->isLoggedIn()) {
                    $byi_url_id = '';
                    $responseArr['welcome_text'] = 'Welcome, '. ucfirst($this->_customerSession->getCustomer()->getName());
                    $wishListItemCollection = $this->_cartHelper->getWishlistData($customer->getId(), $storeId, $byi_url_id);
                    $responseArr['status'] = 'true';
                    $responseArr['message'] = 'Successfully logged in.';
                    $responseArr['cart_count'] = $this->getCartCount();
                    $responseArr['wishlist_count'] = count($wishListItemCollection);
                    $responseArr['customer_id'] = $customer->getId();
                    $responseArr['firstname'] = $customer->getFirstname();
                    $responseArr['lastname'] = $customer->getLastname();
                    $session_id = $customer->getId() . '_' . base64_encode($email);
                    $hash = $customer->getData("password_hash");

                    $subscriber_data = $this->_newsletterModel->loadByEmail($postData['email']);
                    if ($subscriber_data->getId()) {
                        if ($subscriber_data->getSubscriberStatus() == 1) {
                            $responseArr['is_subscribed'] = $subscriber_data->getSubscriberStatus();
                        } else {
                            $responseArr['is_subscribed'] = 0;
                        }
                    } else {
                        $responseArr['is_subscribed'] = 0;
                    }
                    if ($session_id) {
                        $responseArr['session_id'] = $session_id;
                        $data = array('username' => $email, 'password' => $hash, 'devicetoken' => $postData['device_token'], 'customer_id' => $customer->getId(), 'is_logout' => 0);
                        $this->_cartHelper->addTokenGeneralLogin($data);
                    }
                    $responseArr['byi_enabled'] = false;
                }
            }
            $jsonResult->setData($responseArr);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    public function validateHash($password, $hash, $hashArr)
    {
        $hashArr = explode(':', $hash);
        switch (count($hashArr)) {
            case 1:
                return $this->hash($password) === $hash;
            case 2:
                return $this->hash($hashArr[1] . $password) === $hashArr[0];
        }
        return 'Invalid hash.';
    }
    public function getCartCount()
    {
        $count = $this->_cartModel->getQuote()->getItemsCount();
        if (!isset($count)) {
            $count = "0";
        } else {
            $count = $this->_cartModel->getQuote()->getItemsCount();
        }
        return $count;
    }
}
