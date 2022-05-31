<?php
namespace Magento\Shipping\Model\Rate\CarrierResult;

/**
 * Interceptor class for @see \Magento\Shipping\Model\Rate\CarrierResult
 */
class Interceptor extends \Magento\Shipping\Model\Rate\CarrierResult implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function appendResult(\Magento\Shipping\Model\Rate\Result $result, bool $appendFailed) : void
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'appendResult');
        if (!$pluginInfo) {
            parent::appendResult($result, $appendFailed);
        } else {
            $this->___callPlugins('appendResult', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAllRates()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllRates');
        if (!$pluginInfo) {
            return parent::getAllRates();
        } else {
            return $this->___callPlugins('getAllRates', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getError');
        if (!$pluginInfo) {
            return parent::getError();
        } else {
            return $this->___callPlugins('getError', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRateById($id)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRateById');
        if (!$pluginInfo) {
            return parent::getRateById($id);
        } else {
            return $this->___callPlugins('getRateById', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCheapestRate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCheapestRate');
        if (!$pluginInfo) {
            return parent::getCheapestRate();
        } else {
            return $this->___callPlugins('getCheapestRate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRatesByCarrier($carrier)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRatesByCarrier');
        if (!$pluginInfo) {
            return parent::getRatesByCarrier($carrier);
        } else {
            return $this->___callPlugins('getRatesByCarrier', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function asArray()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'asArray');
        if (!$pluginInfo) {
            return parent::asArray();
        } else {
            return $this->___callPlugins('asArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sortRatesByPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'sortRatesByPrice');
        if (!$pluginInfo) {
            return parent::sortRatesByPrice();
        } else {
            return $this->___callPlugins('sortRatesByPrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateRatePrice($packageCount)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'updateRatePrice');
        if (!$pluginInfo) {
            return parent::updateRatePrice($packageCount);
        } else {
            return $this->___callPlugins('updateRatePrice', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'reset');
        if (!$pluginInfo) {
            return parent::reset();
        } else {
            return $this->___callPlugins('reset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setError($error)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setError');
        if (!$pluginInfo) {
            return parent::setError($error);
        } else {
            return $this->___callPlugins('setError', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function append($result)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'append');
        if (!$pluginInfo) {
            return parent::append($result);
        } else {
            return $this->___callPlugins('append', func_get_args(), $pluginInfo);
        }
    }
}
