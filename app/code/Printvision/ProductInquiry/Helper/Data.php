<?php
namespace Printvision\ProductInquiry\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->_customerSession = $customerSession->create();
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        return !empty(trim($this->_customerSession->getCustomer()->getName())) ?
            $this->_customerSession->getCustomer()->getName() : '';
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        return !empty(trim($this->_customerSession->getCustomer()->getEmail())) ?
            $this->_customerSession->getCustomer()->getEmail() : '';
    }

    public function getInquiryEnabledGlobally()
    {
        return $this->getConfig('product_inquiry/general/enable');
    }

    public function getInquiryTitleGlobally()
    {
        return $this->getConfig('product_inquiry/general/label');
    }

    public function getAllowAddtocartGlobally()
    {
        return $this->getConfig('product_inquiry/general/is_addtocart_allowed');
    }

    public function getDisclosePriceGlobally()
    {
        return $this->getConfig('product_inquiry/general/is_product_price_disclosed');
    }

    public function getInquiryEnabledProduct($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        return $product->getIsProductInquiryEnable() != null ? $product->getIsProductInquiryEnable() : 0;
    }

    public function getInquiryTitleProduct($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        return $product->getProductInquiryLabel() != null ? $product->getProductInquiryLabel() : '';
    }

    public function getAllowAddtocartProduct($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        return $product->getIsAddtocartAllowed() != null ? $product->getIsAddtocartAllowed() : 1;
    }

    public function getDisclosePriceProduct($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        return $product->getIsProductPriceDisclosed() != null ? $product->getIsProductPriceDisclosed() : 1;
    }

    public function getInquiryEnabled($productId)
    {
        if ($this->getInquiryEnabledProduct($productId)) {
            return true;
        } elseif ($this->getInquiryEnabledGlobally()) {
            return true;
        } else {
            return false;
        }
    }

    public function getInquiryTitle($productId)
    {
        $productInquiryTitle = '';
        if ($this->getInquiryEnabledProduct($productId)) {
            $productInquiryTitle = $this->getInquiryTitleProduct($productId);
        } elseif ($this->getInquiryEnabledGlobally()) {
            if ($this->getInquiryTitleGlobally() != null) {
                $productInquiryTitle = $this->getInquiryTitleGlobally();
            }
        }
        return $productInquiryTitle;
    }

    public function getAllowAddtocart($productId)
    {
        $allowAddtocart = 1;
        if ($this->getInquiryEnabledProduct($productId)) {
            $allowAddtocart = $this->getAllowAddtocartProduct($productId);
        } elseif ($this->getInquiryEnabledGlobally()) {
            if ($this->getAllowAddtocartGlobally() != null) {
                $allowAddtocart = $this->getAllowAddtocartGlobally();
            }
        }
        return $allowAddtocart;
    }

    public function getDisclosePrice($productId)
    {
        $disclosePrice = 1;
        if ($this->getInquiryEnabledProduct($productId)) {
            $disclosePrice = $this->getDisclosePriceProduct($productId);
        } elseif ($this->getInquiryEnabledGlobally()) {
            if ($this->getDisclosePriceGlobally() != null) {
                $disclosePrice = $this->getDisclosePriceGlobally();
            }
        }

        return $disclosePrice;
    }
}
