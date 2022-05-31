<?php
namespace Magento\Catalog\Model\Config;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Config
 */
class Interceptor extends \Magento\Catalog\Model\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\CacheInterface $cache, \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory, \Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory $entityTypeCollectionFactory, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\Validator\UniversalFactory $universalFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Catalog\Model\ResourceModel\ConfigFactory $configFactory, \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setCollectionFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Eav\Model\Config $eavConfig, ?\Magento\Framework\Serialize\SerializerInterface $serializer = null)
    {
        $this->___init();
        parent::__construct($cache, $entityTypeFactory, $entityTypeCollectionFactory, $cacheState, $universalFactory, $scopeConfig, $configFactory, $productTypeFactory, $groupCollectionFactory, $setCollectionFactory, $storeManager, $eavConfig, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setStoreId');
        if (!$pluginInfo) {
            return parent::setStoreId($storeId);
        } else {
            return $this->___callPlugins('setStoreId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStoreId');
        if (!$pluginInfo) {
            return parent::getStoreId();
        } else {
            return $this->___callPlugins('getStoreId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeSets()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadAttributeSets');
        if (!$pluginInfo) {
            return parent::loadAttributeSets();
        } else {
            return $this->___callPlugins('loadAttributeSets', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSetName($entityTypeId, $id)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeSetName');
        if (!$pluginInfo) {
            return parent::getAttributeSetName($entityTypeId, $id);
        } else {
            return $this->___callPlugins('getAttributeSetName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSetId($entityTypeId, $name = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeSetId');
        if (!$pluginInfo) {
            return parent::getAttributeSetId($entityTypeId, $name);
        } else {
            return $this->___callPlugins('getAttributeSetId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeGroups()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadAttributeGroups');
        if (!$pluginInfo) {
            return parent::loadAttributeGroups();
        } else {
            return $this->___callPlugins('loadAttributeGroups', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeGroupName($attributeSetId, $id)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeGroupName');
        if (!$pluginInfo) {
            return parent::getAttributeGroupName($attributeSetId, $id);
        } else {
            return $this->___callPlugins('getAttributeGroupName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeGroupId($attributeSetId, $name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeGroupId');
        if (!$pluginInfo) {
            return parent::getAttributeGroupId($attributeSetId, $name);
        } else {
            return $this->___callPlugins('getAttributeGroupId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadProductTypes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadProductTypes');
        if (!$pluginInfo) {
            return parent::loadProductTypes();
        } else {
            return $this->___callPlugins('loadProductTypes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypeId($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductTypeId');
        if (!$pluginInfo) {
            return parent::getProductTypeId($name);
        } else {
            return $this->___callPlugins('getProductTypeId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypeName($id)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductTypeName');
        if (!$pluginInfo) {
            return parent::getProductTypeName($id);
        } else {
            return $this->___callPlugins('getProductTypeName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceOptionId($source, $value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSourceOptionId');
        if (!$pluginInfo) {
            return parent::getSourceOptionId($source, $value);
        } else {
            return $this->___callPlugins('getSourceOptionId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductAttributes');
        if (!$pluginInfo) {
            return parent::getProductAttributes();
        } else {
            return $this->___callPlugins('getProductAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesUsedInProductListing()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributesUsedInProductListing');
        if (!$pluginInfo) {
            return parent::getAttributesUsedInProductListing();
        } else {
            return $this->___callPlugins('getAttributesUsedInProductListing', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesUsedForSortBy()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributesUsedForSortBy');
        if (!$pluginInfo) {
            return parent::getAttributesUsedForSortBy();
        } else {
            return $this->___callPlugins('getAttributesUsedForSortBy', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeUsedForSortByArray()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeUsedForSortByArray');
        if (!$pluginInfo) {
            return parent::getAttributeUsedForSortByArray();
        } else {
            return $this->___callPlugins('getAttributeUsedForSortByArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductListDefaultSortBy($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductListDefaultSortBy');
        if (!$pluginInfo) {
            return parent::getProductListDefaultSortBy($store);
        } else {
            return $this->___callPlugins('getProductListDefaultSortBy', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCache');
        if (!$pluginInfo) {
            return parent::getCache();
        } else {
            return $this->___callPlugins('getCache', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'clear');
        if (!$pluginInfo) {
            return parent::clear();
        } else {
            return $this->___callPlugins('clear', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCacheEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isCacheEnabled');
        if (!$pluginInfo) {
            return parent::isCacheEnabled();
        } else {
            return $this->___callPlugins('isCacheEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType($code)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityType');
        if (!$pluginInfo) {
            return parent::getEntityType($code);
        } else {
            return $this->___callPlugins('getEntityType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($entityType)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributes');
        if (!$pluginInfo) {
            return parent::getAttributes($entityType);
        } else {
            return $this->___callPlugins('getAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($entityType, $code)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttribute');
        if (!$pluginInfo) {
            return parent::getAttribute($entityType, $code);
        } else {
            return $this->___callPlugins('getAttribute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityAttributeCodes($entityType, $object = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityAttributeCodes');
        if (!$pluginInfo) {
            return parent::getEntityAttributeCodes($entityType, $object);
        } else {
            return $this->___callPlugins('getEntityAttributeCodes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityAttributes($entityType, $object = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityAttributes');
        if (!$pluginInfo) {
            return parent::getEntityAttributes($entityType, $object);
        } else {
            return $this->___callPlugins('getEntityAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function importAttributesData($entityType, array $attributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'importAttributesData');
        if (!$pluginInfo) {
            return parent::importAttributesData($entityType, $attributes);
        } else {
            return $this->___callPlugins('importAttributesData', func_get_args(), $pluginInfo);
        }
    }
}
