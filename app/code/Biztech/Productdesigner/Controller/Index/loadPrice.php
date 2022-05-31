<?php


namespace Biztech\Productdesigner\Controller\Index;

class loadPrice extends \Magento\Framework\App\Action\Action {

    protected $_storeManager;
    protected $_helper;
    protected $_infoHelper;
    protected $directoryHelper;
    protected $currentCurrencyCode;
    protected $product;
    protected $associatedProduct;
    protected $_priceHelper;
    protected $_taxprice;
    protected $_taxhelper;
    protected $customerSession;
    protected $productModel;
    protected $calculationModel;
    protected $productTypeModel;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Directory\Helper\Data $directoryHelper, \Magento\Framework\Pricing\Helper\Data $priceHelper, \Magento\Catalog\Helper\Data $taxprice, \Magento\Tax\Helper\Data $taxhelper, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Model\ProductFactory $productModel, \Magento\Tax\Model\Calculation $calculationModel, \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $productTypeModel
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_infoHelper = $infoHelper;
        $this->directoryHelper = $directoryHelper;
        $this->_priceHelper = $priceHelper;
        $this->_taxprice = $taxprice;
        $this->_taxhelper = $taxhelper;
        $this->customerSession = $customerSession;
        $this->productModel = $productModel;
        $this->calculationModel = $calculationModel;
        $this->productTypeModel = $productTypeModel;
        parent::__construct($context);
    }

    const Identifier = 'loadPrice';

    public function execute() {
        try {

            $data = json_decode(file_get_contents('php://input'), TRUE);

            $cacheKey = self::Identifier . $data['product_id'] . $data['associated_id'];
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $store = $this->_storeManager->getStore();
                $storeId = $store->getId();
                $productId = ($data['product_id']) ?: '';
                $associatedProductId = ($data['associated_id']) ?: '';

                $this->currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
                $this->product = $this->productModel->create()->load($productId);
                $this->associatedProduct = $this->productModel->create()->load($associatedProductId);

                $response['data'] = $this->_infoHelper->getJsonPrice($productId, $this->currentCurrencyCode);
                $productPrice = $this->getProductPrice($productId);
                $response['data']['productPrice'] = $productPrice;
                $response['data']['productOrignalPrice'] = $this->associatedProduct->getPrice();
                $fixedPrice = $response['data']['additionalFixedPrice'];
                $price = $this->getTierProductData($productId, $fixedPrice);
                $response['data']['tierPrice'] = $price;

                $SpecialPrice = $this->directoryHelper->currencyConvert((float) $this->associatedProduct->getSpecialPrice(), $this->_storeManager->getStore()->getBaseCurrencyCode(), $this->currentCurrencyCode);
                $SpecialPriceWithTax = $this->directoryHelper->currencyConvert($this->_taxprice->getTaxPrice($this->associatedProduct, (float) $this->associatedProduct->getSpecialPrice(), true), $this->_storeManager->getStore()->getBaseCurrencyCode(), $this->currentCurrencyCode);

                // price of associated product
                $associatedProductId = $data['associated_id'];
                $associatedProduct = $this->productModel->create()->load($associatedProductId);

                $_finalPrice = $this->directoryHelper->currencyConvert($associatedProduct->getFinalPrice(), $this->_storeManager->getStore()->getBaseCurrencyCode(), $this->currentCurrencyCode);

                $_priceInclTax = $this->_taxprice->getTaxPrice($this->product, $_finalPrice, true);
                $_priceExclTax = $this->_taxprice->getTaxPrice($this->product, $_finalPrice, false);

                /*print_r($this->product->getPrice());exit();*/

                $response['data']['priceInclTax'] = $this->_priceHelper->currency($_priceInclTax, false, false);
                $response['data']['priceExclTax'] = $this->_priceHelper->currency($_priceExclTax, false, false);
                $response['data']['productName'] = $this->product->getName();
                $response['data']['includeTax'] = $this->_taxhelper->priceIncludesTax() ? 'true' : 'false';
                $response['data']['showIncludeTax'] = $this->_taxhelper->displayPriceIncludingTax();
                $response['data']['showExcludeTax'] = $this->_taxhelper->displayPriceExcludingTax();
                $response['data']['showBothPrices'] = $this->_taxhelper->displayBothPrices();
                $response['data']['SpecialPrice'] = ($SpecialPrice != "") ? $SpecialPrice : '';

                $response['data']['SpecialPriceWithTax'] = ($SpecialPriceWithTax != "") ? $SpecialPriceWithTax : '';
                $_request = $this->calculationModel->getRateRequest(false, false, false);
                $_request->setProductClassId($this->product->getTaxClassId());
                $defaultTax = $this->calculationModel->getRate($_request);

                $response['data']['tax'] = $defaultTax;

                $this->_infoHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    public function getTierProductData($productId, $fixedPrice) {
        if ($this->product->getTypeId() == 'configurable') {
            $childProductsIDs = $this->productTypeModel->create()->getChildrenIds($productId);
            foreach ($childProductsIDs[0] as $childProductsID) {
                $childProduct = $this->productModel->create()->load($childProductsID);
                $finalTierPrice[$childProductsID] = $this->getTierPrices($childProduct, $fixedPrice);
            }
        } else {
            $finalTierPrice[$productId] = $this->getTierPrices($this->product, $fixedPrice);
        }
        return $finalTierPrice;
    }

    public function getTierPrices($product, $fixedPrice) {
        $productTierPrices = $product->getTierPrice();
        $finalTierPrice = array();
        $customerGroupID = 0;
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $customerGroupID = $customer->getGroupId();
        }
        foreach ($productTierPrices as $productTierPrice) {
            if ($productTierPrice['all_groups'] == "1" || $productTierPrice['cust_group'] == $customerGroupID) {
                $productTierPrice['percentage_value'] = number_format($productTierPrice['percentage_value']);
                $productTierPrice['price_qty'] = number_format($productTierPrice['price_qty']);
                $productTierPrice['price'] = $this->directoryHelper->currencyConvert($productTierPrice['price'], $this->_storeManager->getStore()->getBaseCurrencyCode(), $this->currentCurrencyCode);
                $productTierPrice['website_price'] = $productTierPrice['website_price'] + $fixedPrice;
                $productTierPrice['website_price'] = $this->directoryHelper->currencyConvert($productTierPrice['website_price'], $this->_storeManager->getStore()->getBaseCurrencyCode(), $this->currentCurrencyCode);
                $productTierPrice['price_with_Tax'] = $this->directoryHelper->currencyConvert($this->_taxprice->getTaxPrice($product, (float) $productTierPrice['website_price'], true), $this->_storeManager->getStore()->getBaseCurrencyCode(), $this->currentCurrencyCode);
                $finalTierPrice[] = $productTierPrice;
            }
        }
        return $finalTierPrice;
    }

    public function getProductPrice($productId) {
        if ($this->product->getTypeId() == 'configurable') {
            $confProduct = $this->productTypeModel->create();
            $childProductsIDs = $confProduct->getChildrenIds($productId);
            foreach ($childProductsIDs[0] as $childProductsID) {
                $childProduct = $this->productModel->create()->load($childProductsID);
                $childProductPrice = $this->directoryHelper->currencyConvert($childProduct->getFinalPrice(), $this->_storeManager->getStore()->getBaseCurrencyCode(), $this->currentCurrencyCode);
                $prices[$childProduct->getId()] = $childProductPrice;
            }
        } else {
            $prices[$productId] = $this->directoryHelper->currencyConvert($this->product->getFinalPrice(), $this->_storeManager->getStore()->getBaseCurrencyCode(), $this->currentCurrencyCode);
        }
        return $prices;
    }

}
