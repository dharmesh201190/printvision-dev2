<?php
namespace Biztech\Productdesigner\Controller\Customer;
use Magento\Customer\Api\AccountManagementInterface;

class Register extends \Biztech\Productdesigner\Controller\Customer {
	
	public function execute() {		
		if ($this->customerSession->isLoggedIn()) {
			$result['status'] = 'error';
			$result['error'] = "User is already logged in!";
			$this->getResponse()->setBody(json_encode($result));
		}		
		$payload = json_decode(file_get_contents('php://input'), TRUE);
		$this->customerSession->regenerateId();
		try {
			$address = $this->extractDesignAddress();
			$addresses = $address === null ? [] : [$address];
			
			$this->setPostValues($payload);
			
			$customer = $this->customerExtractor->extract('customer_account_create', $this->getRequest());			
			$customer->setAddresses($addresses);
			$password = base64_decode($payload['password']);
			$confirmation = base64_decode($payload['password_confirmation']);
			
			$redirectUrl = $this->customerSession->getBeforeAuthUrl();
			$this->checkDesignPasswordConfirmation($password, $confirmation);
			$customer = $this->customerAccountManagement
			->createAccount($customer, $password, $redirectUrl);
			if ($payload['isSubscribed'] == true) {				
				$this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
			}			
			$this->_eventManager->dispatch(
				'customer_register_success', ['account_controller' => $this, 'customer' => $customer]
			);
			$confirmationStatus = $this->customerAccountManagement->getConfirmationStatus($customer->getId());
			if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
				$email = $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
				
				$result['error'] = __('You must confirm your account.');
			} else {
				$this->customerSession->setCustomerDataAsLoggedIn($customer);
			}
			$result['status'] = 'success';
			$result['customer_id'] = $customer->getId();
			$result['customer_login'] = 1;
			$this->customerSession->setCustomerFormData($payload);
			$this->getResponse()->setBody(json_encode($result));
		} catch (StateException $e) {
			$this->setCatch($e);
		} catch (InputException $e) {
			$this->setCatch($e);
		} catch (\Exception $e) {
			$this->setCatch($e);
		}
	}

	protected function setPostValues($payload) {
		$this->getRequest()->setPostValue('firstname', $payload['firstname']);
		$this->getRequest()->setPostValue('lastname', $payload['lastname']);
		$this->getRequest()->setPostValue('email', $payload['email']);
		$this->getRequest()->setPostValue('password', $payload['password']);
		$this->getRequest()->setPostValue('password_confirmation', $payload['password_confirmation']);
		$this->getRequest()->setPostValue('is_subscribed', $payload['isSubscribed']);
		$this->getRequest()->setPostValue('customer_action', $payload['customer_action']);
	}
	protected function checkDesignPasswordConfirmation($password, $confirmation) {
		if ($password != $confirmation) {
			throw new InputException(__('Please make sure your passwords match.'));
		}
	}

	protected function getSuccessMessage() {
		if ($this->addressHelper->isVatValidationEnabled()) {
			if ($this->addressHelper->getTaxCalculationAddressType() == Address::TYPE_SHIPPING) {
				// @codingStandardsIgnoreStart
				$message = __(
					'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.', $this->urlModel->getUrl('customer/address/edit')
				);
				
			} else {
				
				$message = __(
					'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.', $this->urlModel->getUrl('customer/address/edit')
				);
				
			}
		} else {
			$message = __('Thank you for registering with %1.', $this->_storeManager->getStore()->getFrontendName());
		}
		return $message;
	}

	protected function extractDesignAddress() {
		if (!$this->getRequest()->getPost('create_address')) {
			return null;
		}

		$addressForm = $this->formFactory->create('customer_address', 'customer_register_address');
		$allowedAttributes = $addressForm->getAllowedAttributes();

		$addressData = [];

		$regionDataObject = $this->regionDataFactory->create();
		foreach ($allowedAttributes as $attribute) {
			$attributeCode = $attribute->getAttributeCode();
			$value = $this->getRequest()->getParam($attributeCode);
			if ($value === null) {
				continue;
			}
			switch ($attributeCode) {
				case 'region_id':
				$regionDataObject->setRegionId($value);
				break;
				case 'region':
				$regionDataObject->setRegion($value);
				break;
				default:
				$addressData[$attributeCode] = $value;
			}
		}
		$addressDataObject = $this->addressDataFactory->create();
		$this->dataObjectHelper->populateWithArray(
			$addressDataObject, $addressData, '\Magento\Customer\Api\Data\AddressInterface'
		);
		$addressDataObject->setRegion($regionDataObject);

		$addressDataObject->setIsDefaultBilling(
			$this->getRequest()->getParam('default_billing', false)
		)->setIsDefaultShipping(
			$this->getRequest()->getParam('default_shipping', false)
		);
		return $addressDataObject;
	}

	protected function setCatch($e) {
		$response = $this->_infoHelper->throwException($e, self::class);
		$response['message'] = $e->getMessage();
		$this->getResponse()->setBody(json_encode($response));
	}

}
