<?php
namespace Magento\Quote\Model\Quote\Item;

/**
 * Interceptor class for @see \Magento\Quote\Model\Quote\Item
 */
class Interceptor extends \Magento\Quote\Model\Quote\Item implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Sales\Model\Status\ListFactory $statusListFactory, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Quote\Model\Quote\Item\OptionFactory $itemOptionFactory, \Magento\Quote\Model\Quote\Item\Compare $quoteItemCompare, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = [], ?\Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $productRepository, $priceCurrency, $statusListFactory, $localeFormat, $itemOptionFactory, $quoteItemCompare, $stockRegistry, $resource, $resourceCollection, $data, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'beforeSave');
        if (!$pluginInfo) {
            return parent::beforeSave();
        } else {
            return $this->___callPlugins('beforeSave', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAddress');
        if (!$pluginInfo) {
            return parent::getAddress();
        } else {
            return $this->___callPlugins('getAddress', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setQuote');
        if (!$pluginInfo) {
            return parent::setQuote($quote);
        } else {
            return $this->___callPlugins('setQuote', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQuote()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQuote');
        if (!$pluginInfo) {
            return parent::getQuote();
        } else {
            return $this->___callPlugins('getQuote', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addQty($qty)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addQty');
        if (!$pluginInfo) {
            return parent::addQty($qty);
        } else {
            return $this->___callPlugins('addQty', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setQty');
        if (!$pluginInfo) {
            return parent::setQty($qty);
        } else {
            return $this->___callPlugins('setQty', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyOptions()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQtyOptions');
        if (!$pluginInfo) {
            return parent::getQtyOptions();
        } else {
            return $this->___callPlugins('getQtyOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyOptions($qtyOptions)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setQtyOptions');
        if (!$pluginInfo) {
            return parent::setQtyOptions($qtyOptions);
        } else {
            return $this->___callPlugins('setQtyOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct($product)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setProduct');
        if (!$pluginInfo) {
            return parent::setProduct($product);
        } else {
            return $this->___callPlugins('setProduct', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function representProduct($product)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'representProduct');
        if (!$pluginInfo) {
            return parent::representProduct($product);
        } else {
            return $this->___callPlugins('representProduct', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function compareOptions($options1, $options2)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'compareOptions');
        if (!$pluginInfo) {
            return parent::compareOptions($options1, $options2);
        } else {
            return $this->___callPlugins('compareOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function compare($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'compare');
        if (!$pluginInfo) {
            return parent::compare($item);
        } else {
            return $this->___callPlugins('compare', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductType()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductType');
        if (!$pluginInfo) {
            return parent::getProductType();
        } else {
            return $this->___callPlugins('getProductType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRealProductType()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRealProductType');
        if (!$pluginInfo) {
            return parent::getRealProductType();
        } else {
            return $this->___callPlugins('getRealProductType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $arrAttributes = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toArray');
        if (!$pluginInfo) {
            return parent::toArray($arrAttributes);
        } else {
            return $this->___callPlugins('toArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setOptions');
        if (!$pluginInfo) {
            return parent::setOptions($options);
        } else {
            return $this->___callPlugins('setOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOptions');
        if (!$pluginInfo) {
            return parent::getOptions();
        } else {
            return $this->___callPlugins('getOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsByCode()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOptionsByCode');
        if (!$pluginInfo) {
            return parent::getOptionsByCode();
        } else {
            return $this->___callPlugins('getOptionsByCode', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addOption($option)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addOption');
        if (!$pluginInfo) {
            return parent::addOption($option);
        } else {
            return $this->___callPlugins('addOption', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateQtyOption(\Magento\Framework\DataObject $option, $value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'updateQtyOption');
        if (!$pluginInfo) {
            return parent::updateQtyOption($option, $value);
        } else {
            return $this->___callPlugins('updateQtyOption', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption($code)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeOption');
        if (!$pluginInfo) {
            return parent::removeOption($code);
        } else {
            return $this->___callPlugins('removeOption', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionByCode($code)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOptionByCode');
        if (!$pluginInfo) {
            return parent::getOptionByCode($code);
        } else {
            return $this->___callPlugins('getOptionByCode', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveItemOptions()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveItemOptions');
        if (!$pluginInfo) {
            return parent::saveItemOptions();
        } else {
            return $this->___callPlugins('saveItemOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setIsOptionsSaved($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setIsOptionsSaved');
        if (!$pluginInfo) {
            return parent::setIsOptionsSaved($flag);
        } else {
            return $this->___callPlugins('setIsOptionsSaved', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOptionsSaved()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isOptionsSaved');
        if (!$pluginInfo) {
            return parent::isOptionsSaved();
        } else {
            return $this->___callPlugins('isOptionsSaved', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'afterSave');
        if (!$pluginInfo) {
            return parent::afterSave();
        } else {
            return $this->___callPlugins('afterSave', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBuyRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBuyRequest');
        if (!$pluginInfo) {
            return parent::getBuyRequest();
        } else {
            return $this->___callPlugins('getBuyRequest', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setHasError($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setHasError');
        if (!$pluginInfo) {
            return parent::setHasError($flag);
        } else {
            return $this->___callPlugins('setHasError', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addErrorInfo($origin = null, $code = null, $message = null, $additionalData = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addErrorInfo');
        if (!$pluginInfo) {
            return parent::addErrorInfo($origin, $code, $message, $additionalData);
        } else {
            return $this->___callPlugins('addErrorInfo', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorInfos()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getErrorInfos');
        if (!$pluginInfo) {
            return parent::getErrorInfos();
        } else {
            return $this->___callPlugins('getErrorInfos', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeErrorInfosByParams($params)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeErrorInfosByParams');
        if (!$pluginInfo) {
            return parent::removeErrorInfosByParams($params);
        } else {
            return $this->___callPlugins('removeErrorInfosByParams', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getItemId');
        if (!$pluginInfo) {
            return parent::getItemId();
        } else {
            return $this->___callPlugins('getItemId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($itemID)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setItemId');
        if (!$pluginInfo) {
            return parent::setItemId($itemID);
        } else {
            return $this->___callPlugins('setItemId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSku');
        if (!$pluginInfo) {
            return parent::getSku();
        } else {
            return $this->___callPlugins('getSku', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setSku');
        if (!$pluginInfo) {
            return parent::setSku($sku);
        } else {
            return $this->___callPlugins('setSku', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQty');
        if (!$pluginInfo) {
            return parent::getQty();
        } else {
            return $this->___callPlugins('getQty', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getName');
        if (!$pluginInfo) {
            return parent::getName();
        } else {
            return $this->___callPlugins('getName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setName');
        if (!$pluginInfo) {
            return parent::setName($name);
        } else {
            return $this->___callPlugins('setName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPrice');
        if (!$pluginInfo) {
            return parent::getPrice();
        } else {
            return $this->___callPlugins('getPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setPrice');
        if (!$pluginInfo) {
            return parent::setPrice($price);
        } else {
            return $this->___callPlugins('setPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setProductType($productType)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setProductType');
        if (!$pluginInfo) {
            return parent::setProductType($productType);
        } else {
            return $this->___callPlugins('setProductType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQuoteId');
        if (!$pluginInfo) {
            return parent::getQuoteId();
        } else {
            return $this->___callPlugins('getQuoteId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteId($quoteId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setQuoteId');
        if (!$pluginInfo) {
            return parent::setQuoteId($quoteId);
        } else {
            return $this->___callPlugins('setQuoteId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductOption()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductOption');
        if (!$pluginInfo) {
            return parent::getProductOption();
        } else {
            return $this->___callPlugins('getProductOption', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setProductOption(\Magento\Quote\Api\Data\ProductOptionInterface $productOption)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setProductOption');
        if (!$pluginInfo) {
            return parent::setProductOption($productOption);
        } else {
            return $this->___callPlugins('setProductOption', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExtensionAttributes');
        if (!$pluginInfo) {
            return parent::getExtensionAttributes();
        } else {
            return $this->___callPlugins('getExtensionAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Magento\Quote\Api\Data\CartItemExtensionInterface $extensionAttributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setExtensionAttributes');
        if (!$pluginInfo) {
            return parent::setExtensionAttributes($extensionAttributes);
        } else {
            return $this->___callPlugins('setExtensionAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProduct');
        if (!$pluginInfo) {
            return parent::getProduct();
        } else {
            return $this->___callPlugins('getProduct', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFileDownloadParams()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFileDownloadParams');
        if (!$pluginInfo) {
            return parent::getFileDownloadParams();
        } else {
            return $this->___callPlugins('getFileDownloadParams', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setParentItem($parentItem)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setParentItem');
        if (!$pluginInfo) {
            return parent::setParentItem($parentItem);
        } else {
            return $this->___callPlugins('setParentItem', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParentItem()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getParentItem');
        if (!$pluginInfo) {
            return parent::getParentItem();
        } else {
            return $this->___callPlugins('getParentItem', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getChildren');
        if (!$pluginInfo) {
            return parent::getChildren();
        } else {
            return $this->___callPlugins('getChildren', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($child)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addChild');
        if (!$pluginInfo) {
            return parent::addChild($child);
        } else {
            return $this->___callPlugins('addChild', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($messages)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setMessage');
        if (!$pluginInfo) {
            return parent::setMessage($messages);
        } else {
            return $this->___callPlugins('setMessage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addMessage($message)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addMessage');
        if (!$pluginInfo) {
            return parent::addMessage($message);
        } else {
            return $this->___callPlugins('addMessage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage($string = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMessage');
        if (!$pluginInfo) {
            return parent::getMessage($string);
        } else {
            return $this->___callPlugins('getMessage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeMessageByText($text)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeMessageByText');
        if (!$pluginInfo) {
            return parent::removeMessageByText($text);
        } else {
            return $this->___callPlugins('removeMessageByText', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearMessage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'clearMessage');
        if (!$pluginInfo) {
            return parent::clearMessage();
        } else {
            return $this->___callPlugins('clearMessage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStore');
        if (!$pluginInfo) {
            return parent::getStore();
        } else {
            return $this->___callPlugins('getStore', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkData');
        if (!$pluginInfo) {
            return parent::checkData();
        } else {
            return $this->___callPlugins('checkData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalQty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTotalQty');
        if (!$pluginInfo) {
            return parent::getTotalQty();
        } else {
            return $this->___callPlugins('getTotalQty', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function calcRowTotal()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'calcRowTotal');
        if (!$pluginInfo) {
            return parent::calcRowTotal();
        } else {
            return $this->___callPlugins('calcRowTotal', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculationPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCalculationPrice');
        if (!$pluginInfo) {
            return parent::getCalculationPrice();
        } else {
            return $this->___callPlugins('getCalculationPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculationPriceOriginal()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCalculationPriceOriginal');
        if (!$pluginInfo) {
            return parent::getCalculationPriceOriginal();
        } else {
            return $this->___callPlugins('getCalculationPriceOriginal', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCalculationPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBaseCalculationPrice');
        if (!$pluginInfo) {
            return parent::getBaseCalculationPrice();
        } else {
            return $this->___callPlugins('getBaseCalculationPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCalculationPriceOriginal()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBaseCalculationPriceOriginal');
        if (!$pluginInfo) {
            return parent::getBaseCalculationPriceOriginal();
        } else {
            return $this->___callPlugins('getBaseCalculationPriceOriginal', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOriginalPrice');
        if (!$pluginInfo) {
            return parent::getOriginalPrice();
        } else {
            return $this->___callPlugins('getOriginalPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalPrice($price)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setOriginalPrice');
        if (!$pluginInfo) {
            return parent::setOriginalPrice($price);
        } else {
            return $this->___callPlugins('setOriginalPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseOriginalPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBaseOriginalPrice');
        if (!$pluginInfo) {
            return parent::getBaseOriginalPrice();
        } else {
            return $this->___callPlugins('getBaseOriginalPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomPrice($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCustomPrice');
        if (!$pluginInfo) {
            return parent::setCustomPrice($value);
        } else {
            return $this->___callPlugins('setCustomPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConvertedPrice');
        if (!$pluginInfo) {
            return parent::getConvertedPrice();
        } else {
            return $this->___callPlugins('getConvertedPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedPrice($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setConvertedPrice');
        if (!$pluginInfo) {
            return parent::setConvertedPrice($value);
        } else {
            return $this->___callPlugins('setConvertedPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isChildrenCalculated()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isChildrenCalculated');
        if (!$pluginInfo) {
            return parent::isChildrenCalculated();
        } else {
            return $this->___callPlugins('isChildrenCalculated', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isShipSeparately()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isShipSeparately');
        if (!$pluginInfo) {
            return parent::isShipSeparately();
        } else {
            return $this->___callPlugins('isShipSeparately', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalDiscountAmount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTotalDiscountAmount');
        if (!$pluginInfo) {
            return parent::getTotalDiscountAmount();
        } else {
            return $this->___callPlugins('getTotalDiscountAmount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomAttributes');
        if (!$pluginInfo) {
            return parent::getCustomAttributes();
        } else {
            return $this->___callPlugins('getCustomAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttribute($attributeCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomAttribute');
        if (!$pluginInfo) {
            return parent::getCustomAttribute($attributeCode);
        } else {
            return $this->___callPlugins('getCustomAttribute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCustomAttributes');
        if (!$pluginInfo) {
            return parent::setCustomAttributes($attributes);
        } else {
            return $this->___callPlugins('setCustomAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCustomAttribute');
        if (!$pluginInfo) {
            return parent::setCustomAttribute($attributeCode, $attributeValue);
        } else {
            return $this->___callPlugins('setCustomAttribute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setData');
        if (!$pluginInfo) {
            return parent::setData($key, $value);
        } else {
            return $this->___callPlugins('setData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetData');
        if (!$pluginInfo) {
            return parent::unsetData($key);
        } else {
            return $this->___callPlugins('unsetData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $index = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        if (!$pluginInfo) {
            return parent::getData($key, $index);
        } else {
            return $this->___callPlugins('getData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setId($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setId');
        if (!$pluginInfo) {
            return parent::setId($value);
        } else {
            return $this->___callPlugins('setId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setIdFieldName($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setIdFieldName');
        if (!$pluginInfo) {
            return parent::setIdFieldName($name);
        } else {
            return $this->___callPlugins('setIdFieldName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFieldName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIdFieldName');
        if (!$pluginInfo) {
            return parent::getIdFieldName();
        } else {
            return $this->___callPlugins('getIdFieldName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getId');
        if (!$pluginInfo) {
            return parent::getId();
        } else {
            return $this->___callPlugins('getId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted($isDeleted = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDeleted');
        if (!$pluginInfo) {
            return parent::isDeleted($isDeleted);
        } else {
            return $this->___callPlugins('isDeleted', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasDataChanges()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'hasDataChanges');
        if (!$pluginInfo) {
            return parent::hasDataChanges();
        } else {
            return $this->___callPlugins('hasDataChanges', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDataChanges($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDataChanges');
        if (!$pluginInfo) {
            return parent::setDataChanges($value);
        } else {
            return $this->___callPlugins('setDataChanges', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrigData($key = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOrigData');
        if (!$pluginInfo) {
            return parent::getOrigData($key);
        } else {
            return $this->___callPlugins('getOrigData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOrigData($key = null, $data = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setOrigData');
        if (!$pluginInfo) {
            return parent::setOrigData($key, $data);
        } else {
            return $this->___callPlugins('setOrigData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dataHasChangedFor($field)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dataHasChangedFor');
        if (!$pluginInfo) {
            return parent::dataHasChangedFor($field);
        } else {
            return $this->___callPlugins('dataHasChangedFor', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResourceName');
        if (!$pluginInfo) {
            return parent::getResourceName();
        } else {
            return $this->___callPlugins('getResourceName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollection()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResourceCollection');
        if (!$pluginInfo) {
            return parent::getResourceCollection();
        } else {
            return $this->___callPlugins('getResourceCollection', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCollection');
        if (!$pluginInfo) {
            return parent::getCollection();
        } else {
            return $this->___callPlugins('getCollection', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($modelId, $field = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'load');
        if (!$pluginInfo) {
            return parent::load($modelId, $field);
        } else {
            return $this->___callPlugins('load', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeLoad($identifier, $field = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'beforeLoad');
        if (!$pluginInfo) {
            return parent::beforeLoad($identifier, $field);
        } else {
            return $this->___callPlugins('beforeLoad', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterLoad()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'afterLoad');
        if (!$pluginInfo) {
            return parent::afterLoad();
        } else {
            return $this->___callPlugins('afterLoad', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSaveAllowed()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isSaveAllowed');
        if (!$pluginInfo) {
            return parent::isSaveAllowed();
        } else {
            return $this->___callPlugins('isSaveAllowed', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setHasDataChanges($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setHasDataChanges');
        if (!$pluginInfo) {
            return parent::setHasDataChanges($flag);
        } else {
            return $this->___callPlugins('setHasDataChanges', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save();
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterCommitCallback()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'afterCommitCallback');
        if (!$pluginInfo) {
            return parent::afterCommitCallback();
        } else {
            return $this->___callPlugins('afterCommitCallback', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isObjectNew($flag = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isObjectNew');
        if (!$pluginInfo) {
            return parent::isObjectNew($flag);
        } else {
            return $this->___callPlugins('isObjectNew', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateBeforeSave()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validateBeforeSave');
        if (!$pluginInfo) {
            return parent::validateBeforeSave();
        } else {
            return $this->___callPlugins('validateBeforeSave', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTags()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCacheTags');
        if (!$pluginInfo) {
            return parent::getCacheTags();
        } else {
            return $this->___callPlugins('getCacheTags', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cleanModelCache()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'cleanModelCache');
        if (!$pluginInfo) {
            return parent::cleanModelCache();
        } else {
            return $this->___callPlugins('cleanModelCache', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'delete');
        if (!$pluginInfo) {
            return parent::delete();
        } else {
            return $this->___callPlugins('delete', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'beforeDelete');
        if (!$pluginInfo) {
            return parent::beforeDelete();
        } else {
            return $this->___callPlugins('beforeDelete', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'afterDelete');
        if (!$pluginInfo) {
            return parent::afterDelete();
        } else {
            return $this->___callPlugins('afterDelete', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterDeleteCommit()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'afterDeleteCommit');
        if (!$pluginInfo) {
            return parent::afterDeleteCommit();
        } else {
            return $this->___callPlugins('afterDeleteCommit', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResource');
        if (!$pluginInfo) {
            return parent::getResource();
        } else {
            return $this->___callPlugins('getResource', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityId');
        if (!$pluginInfo) {
            return parent::getEntityId();
        } else {
            return $this->___callPlugins('getEntityId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setEntityId');
        if (!$pluginInfo) {
            return parent::setEntityId($entityId);
        } else {
            return $this->___callPlugins('setEntityId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearInstance()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'clearInstance');
        if (!$pluginInfo) {
            return parent::clearInstance();
        } else {
            return $this->___callPlugins('clearInstance', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStoredData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStoredData');
        if (!$pluginInfo) {
            return parent::getStoredData();
        } else {
            return $this->___callPlugins('getStoredData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEventPrefix()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEventPrefix');
        if (!$pluginInfo) {
            return parent::getEventPrefix();
        } else {
            return $this->___callPlugins('getEventPrefix', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addData(array $arr)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addData');
        if (!$pluginInfo) {
            return parent::addData($arr);
        } else {
            return $this->___callPlugins('addData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByPath($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByPath');
        if (!$pluginInfo) {
            return parent::getDataByPath($path);
        } else {
            return $this->___callPlugins('getDataByPath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByKey($key)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByKey');
        if (!$pluginInfo) {
            return parent::getDataByKey($key);
        } else {
            return $this->___callPlugins('getDataByKey', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDataUsingMethod($key, $args = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDataUsingMethod');
        if (!$pluginInfo) {
            return parent::setDataUsingMethod($key, $args);
        } else {
            return $this->___callPlugins('setDataUsingMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataUsingMethod($key, $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataUsingMethod');
        if (!$pluginInfo) {
            return parent::getDataUsingMethod($key, $args);
        } else {
            return $this->___callPlugins('getDataUsingMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'hasData');
        if (!$pluginInfo) {
            return parent::hasData($key);
        } else {
            return $this->___callPlugins('hasData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToArray(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToArray');
        if (!$pluginInfo) {
            return parent::convertToArray($keys);
        } else {
            return $this->___callPlugins('convertToArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toXml(array $keys = [], $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toXml');
        if (!$pluginInfo) {
            return parent::toXml($keys, $rootName, $addOpenTag, $addCdata);
        } else {
            return $this->___callPlugins('toXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToXml(array $arrAttributes = [], $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToXml');
        if (!$pluginInfo) {
            return parent::convertToXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
        } else {
            return $this->___callPlugins('convertToXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toJson');
        if (!$pluginInfo) {
            return parent::toJson($keys);
        } else {
            return $this->___callPlugins('toJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToJson(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToJson');
        if (!$pluginInfo) {
            return parent::convertToJson($keys);
        } else {
            return $this->___callPlugins('convertToJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toString($format = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toString');
        if (!$pluginInfo) {
            return parent::toString($format);
        } else {
            return $this->___callPlugins('toString', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__call');
        if (!$pluginInfo) {
            return parent::__call($method, $args);
        } else {
            return $this->___callPlugins('__call', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isEmpty');
        if (!$pluginInfo) {
            return parent::isEmpty();
        } else {
            return $this->___callPlugins('isEmpty', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($keys = [], $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'serialize');
        if (!$pluginInfo) {
            return parent::serialize($keys, $valueSeparator, $fieldSeparator, $quote);
        } else {
            return $this->___callPlugins('serialize', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function debug($data = null, &$objects = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'debug');
        if (!$pluginInfo) {
            return parent::debug($data, $objects);
        } else {
            return $this->___callPlugins('debug', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetSet');
        if (!$pluginInfo) {
            return parent::offsetSet($offset, $value);
        } else {
            return $this->___callPlugins('offsetSet', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetExists');
        if (!$pluginInfo) {
            return parent::offsetExists($offset);
        } else {
            return $this->___callPlugins('offsetExists', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetUnset');
        if (!$pluginInfo) {
            return parent::offsetUnset($offset);
        } else {
            return $this->___callPlugins('offsetUnset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetGet');
        if (!$pluginInfo) {
            return parent::offsetGet($offset);
        } else {
            return $this->___callPlugins('offsetGet', func_get_args(), $pluginInfo);
        }
    }
}
