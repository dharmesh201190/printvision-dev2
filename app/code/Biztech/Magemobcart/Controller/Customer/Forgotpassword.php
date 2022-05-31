<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;

class Forgotpassword extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $customerAccountManagement;
    protected $formKey;

    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session     $customerSession
     * @param AccountManagementInterface          $customerAccountManagement
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }
    /**
     * This function is used for set email of reset password.
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
            if (array_key_exists('email', $postData)) {
                $email = $postData['email'];
                if (isset($email)) {
                    if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
                        $this->_customerSession->setForgottenEmail($email);
                        $returnExtensionArray = array('status' => false,'message' => "Please correct the email address.");
                        $jsonResult->setData($returnExtensionArray);
                        return $jsonResult;
                    }

                    try {
                        $this->customerAccountManagement->initiatePasswordReset(
                            $email,
                            AccountManagement::EMAIL_RESET
                        );
                    } catch (NoSuchEntityException $exception) {
                    } catch (SecurityViolationException $exception) {
                        $returnExtensionArray = array('status' => false,'message' => $exception->getMessage());
                        $jsonResult->setData($returnExtensionArray);
                        return $jsonResult;
                    } catch (\Exception $exception) {
                        $exception = $exception->getMessage();
                        $returnExtensionArray = array('status' => false,'message' => $exception);
                        $jsonResult->setData($returnExtensionArray);
                        return $jsonResult;
                    }
                    $returnExtensionArray = array('status' => true,'message' => $this->getSuccessMessage($postData['email']));
                    $jsonResult->setData($returnExtensionArray);
                    return $jsonResult;
                }
            } else {
                $returnExtensionArray = array('status' => false,'message' => "Email address is missing");
                $jsonResult->setData($returnExtensionArray);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    protected function getSuccessMessage($email)
    {
        return 'If there is an account associated with '.$email.' you will receive an email with a link to reset your password.';
    }
}
