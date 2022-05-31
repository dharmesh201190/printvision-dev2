<?php
namespace Magento\Catalog\Model\Layer\FilterList;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Layer\FilterList
 */
class Interceptor extends \Magento\Catalog\Model\Layer\FilterList implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Catalog\Model\Layer\FilterableAttributeListInterface $filterableAttributes, array $filters = [])
    {
        $this->___init();
        parent::__construct($objectManager, $filterableAttributes, $filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFilters');
        if (!$pluginInfo) {
            return parent::getFilters($layer);
        } else {
            return $this->___callPlugins('getFilters', func_get_args(), $pluginInfo);
        }
    }
}
