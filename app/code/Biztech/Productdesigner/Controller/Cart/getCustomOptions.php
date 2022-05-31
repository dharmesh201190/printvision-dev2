<?php

namespace Biztech\Productdesigner\Controller\Cart;

class getCustomOptions extends \Magento\Framework\App\Action\Action {

	// Variable Declaration
	protected $_storeManager;
	protected $_helper;
	protected $directoryHelper;
	protected $_localeCurrency;
	protected $_productManager;
	protected $_infoHelper;
	private $_quoteItemFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Biztech\Productdesigner\Helper\Data $helper,
		\Biztech\Productdesigner\Helper\Info $infoHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Directory\Helper\Data $directoryHelper,
		\Magento\Framework\Locale\CurrencyInterface $localeCurrency,
		\Magento\Catalog\Model\Product $productManager,
		\Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
	) {
		$this->_helper = $helper;
		$this->_localeCurrency = $localeCurrency;
		$this->directoryHelper = $directoryHelper;
		$this->_storeManager = $storeManager;
		$this->_productManager = $productManager;
		$this->_infoHelper = $infoHelper;
		$this->_quoteItemFactory = $quoteItemFactory;
		parent::__construct($context);
	}

	const Identifier = 'getCustomoptions';
	public function execute() {
		try {
			$payload = json_decode(file_get_contents('php://input'), TRUE);
			$productId = $payload['product_id'];
			$itemId = (isset($payload['itemId'])) ? $payload['itemId'] : '';
			$cacheKey = self::Identifier . $productId . $itemId;
			$response = $this->_infoHelper->loadCache($cacheKey);

			if(!$response){
				if(!$payload || !count($payload) || !isset($payload['product_id'])) {
					$response = array(
						'status'=>'failure',
						'log'=>'No Payload'
					);
					$this->getResponse()->setBody(json_encode($response));
				}
				$productId = $payload['product_id'];
				$currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
				$obj_product = $this->_productManager;
				$product = $obj_product->load($productId);
				$all_options = $product->getOptions();
				$customOptionsData = [];
				foreach ($all_options as $option) {
					$custom_options = [];
					$custom_options['id'] = $option->getOptionId();
					$custom_options['type'] = $option->getType();
					$custom_options['title'] = $option->getDefaultTitle();
					$custom_options['isRequire'] = (int) $option->getIsRequire() == 1 ? true : false;

					$optionType = $option->getType();
					$price = 0;
					if ($optionType == 'drop_down' || $optionType == 'multiple' || $optionType == 'checkbox' || $optionType == 'radio') {
						$values = [];
						$optionValues = [];
						foreach ($option->getValues() as $value) {
							if ($value->getPriceType() == 'fixed') {
								$price = $this->directoryHelper->currencyConvert($value->getData('default_price'), $this->_storeManager->getStore()->getBaseCurrencyCode(), $currentCurrencyCode);
							} else {
								$currencyPrice = $this->directoryHelper->currencyConvert($value->getData('default_price'), $this->_storeManager->getStore()->getBaseCurrencyCode(), $currentCurrencyCode);
								$price = ($product->getFinalPrice() * $currencyPrice / 100);
							}
							$values['optionTypeId'] = $value->getOptionTypeId();
							$values['valueTitle'] = $value->getDefaultTitle();
							$values['price'] = $this->_localeCurrency->getCurrency($currentCurrencyCode)->toCurrency($price);
							$values['price_tax'] = $this->_localeCurrency->getCurrency($currentCurrencyCode)->toCurrency($price);
							$values['origprice'] = $price;
							$optionValues[] = $values;
						}
						$custom_options['options_values'] = $optionValues;
					}
					if ($optionType == 'text' || $optionType == 'field' || $optionType == 'area') {
						$custom_options['max_characters'] = $option->getData('max_characters');
						if ($option->getPriceType() == 'fixed') {
							$price = $this->directoryHelper->currencyConvert($option->getData('default_price'), $this->_storeManager->getStore()->getBaseCurrencyCode(), $currentCurrencyCode);
						} else {
							$currencyPrice = $this->directoryHelper->currencyConvert($option->getData('default_price'), $this->_storeManager->getStore()->getBaseCurrencyCode(), $currentCurrencyCode);
							$price = ($product->getFinalPrice() * $currencyPrice / 100);
						}
					}
					if ($optionType == 'file') {
						if ($option->getPriceType() == 'fixed') {
							$price = $this->directoryHelper->currencyConvert($option->getData('default_price'), $this->_storeManager->getStore()->getBaseCurrencyCode(), $currentCurrencyCode);
						} else {
							$currencyPrice = $this->directoryHelper->currencyConvert($option->getData('default_price'), $this->_storeManager->getStore()->getBaseCurrencyCode(), $currentCurrencyCode);
							$price = ($product->getFinalPrice() * $currencyPrice / 100);
						}
						if ($option->getData('file_extension')) {
							$custom_options['file_extension_flag'] = true;
							$custom_options['file_extension'] = $option->getData('file_extension');
						}
					}
					if ($optionType == 'date_time') {
						$date_time_format = $this->_helper->getConfig('catalog/custom_options/time_format');
						if ($date_time_format == '24h') {
							$date_time_options['mincount'] = '60';
							$date_time_options['is24hTimeFormat'] = '24';
							$date_time_options['hourStart'] = 0;
							$date_time_options['hourEnd'] = 23;
						} else {
							$date_time_options['mincount'] = '60';
							$date_time_options['is24hTimeFormat'] = '12';
							$date_time_options['hourStart'] = 1;
							$date_time_options['hourEnd'] = 12;
						}
						$date_time_options['year'] = date("Y");
						$custom_options['date_time'] = $date_time_options;
					}
					if ($optionType == 'date') {
						$date_time_options['year'] = date("Y");
						$custom_options['date'] = $date_time_options;
					}
					if ($optionType == 'time') {
						$time_format = $this->_helper->getConfig('catalog/custom_options/time_format');
						if ($time_format == '24h') {
							$time_options['mincount'] = '60';
							$time_options['is24hTimeFormat'] = '24';
							$time_options['hourStart'] = 0;
							$time_options['hourEnd'] = 23;
						} else {
							$time_options['mincount'] = '60';
							$time_options['is24hTimeFormat'] = '12';
							$time_options['hourStart'] = 1;
							$time_options['hourEnd'] = 12;
						}
						$custom_options['time'] = $time_options;
					}
					if ($optionType != 'drop_down' && $optionType != 'multiple' && $optionType != 'checkbox' && $optionType != 'radio') {
						$custom_options['price'] = $this->_localeCurrency->getCurrency($currentCurrencyCode)->toCurrency($price);
						$custom_options['price_tax'] = $this->_localeCurrency->getCurrency($currentCurrencyCode)->toCurrency($price);
						$custom_options['origprice'] = $price;
					}
					$customOptionsData[] = $custom_options;
				}
				if(isset($payload['itemId'])) {
					$selectedCustomOptions = $this->checkForCustomOptions($payload['itemId']);
					if($selectedCustomOptions != false) {
						$response['selectedCustomOptions'] = $selectedCustomOptions;
					}
				}
				$allowAddMore=$product->getAllowAddMore();
				$response['status'] = "Success";
				$response["custom_options"] = $customOptionsData;
				$response["allow_add_more"] = $allowAddMore;
				$this->_infoHelper->setCache($response, $cacheKey);
			}			
			$this->getResponse()->setBody(json_encode($response));
		} catch(\Exception $e) {
			$response = $this->_infoHelper->throwException($e, self::class);
			$this->getResponse()->setBody(json_encode($response));
		}
	}

	protected function checkForCustomOptions($itemId) {
		$itemId = base64_decode($itemId);
		$quoteItem = $this->_quoteItemFactory->create()->getCollection()->addFieldToFilter('item_id', array('eq'=>$itemId))->getItemById($itemId);		
		$selectedOptions = json_decode($quoteItem->getProduct()->getCustomOption('info_buyRequest')->getData()['value'], true);
		$selectedOptions['options']['qty'] = $selectedOptions['qty'];		
		return (count($selectedOptions) > 0 && isset($selectedOptions['options'])) ? $selectedOptions['options'] : false;
	}

}
