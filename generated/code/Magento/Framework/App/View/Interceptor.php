<?php
namespace Magento\Framework\App\View;

/**
 * Interceptor class for @see \Magento\Framework\App\View
 */
class Interceptor extends \Magento\Framework\App\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\LayoutInterface $layout, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\App\ResponseInterface $response, \Magento\Framework\Config\ScopeInterface $configScope, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Framework\View\Result\PageFactory $pageFactory, \Magento\Framework\App\ActionFlag $actionFlag)
    {
        $this->___init();
        parent::__construct($layout, $request, $response, $configScope, $eventManager, $pageFactory, $actionFlag);
    }

    /**
     * {@inheritdoc}
     */
    public function getPage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPage');
        if (!$pluginInfo) {
            return parent::getPage();
        } else {
            return $this->___callPlugins('getPage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLayout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLayout');
        if (!$pluginInfo) {
            return parent::getLayout();
        } else {
            return $this->___callPlugins('getLayout', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true, $addActionHandles = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadLayout');
        if (!$pluginInfo) {
            return parent::loadLayout($handles, $generateBlocks, $generateXml, $addActionHandles);
        } else {
            return $this->___callPlugins('loadLayout', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLayoutHandle()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDefaultLayoutHandle');
        if (!$pluginInfo) {
            return parent::getDefaultLayoutHandle();
        } else {
            return $this->___callPlugins('getDefaultLayoutHandle', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addActionLayoutHandles()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addActionLayoutHandles');
        if (!$pluginInfo) {
            return parent::addActionLayoutHandles();
        } else {
            return $this->___callPlugins('addActionLayoutHandles', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addPageLayoutHandles(array $parameters = [], $defaultHandle = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addPageLayoutHandles');
        if (!$pluginInfo) {
            return parent::addPageLayoutHandles($parameters, $defaultHandle);
        } else {
            return $this->___callPlugins('addPageLayoutHandles', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadLayoutUpdates()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadLayoutUpdates');
        if (!$pluginInfo) {
            return parent::loadLayoutUpdates();
        } else {
            return $this->___callPlugins('loadLayoutUpdates', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateLayoutXml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'generateLayoutXml');
        if (!$pluginInfo) {
            return parent::generateLayoutXml();
        } else {
            return $this->___callPlugins('generateLayoutXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateLayoutBlocks()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'generateLayoutBlocks');
        if (!$pluginInfo) {
            return parent::generateLayoutBlocks();
        } else {
            return $this->___callPlugins('generateLayoutBlocks', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function renderLayout($output = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'renderLayout');
        if (!$pluginInfo) {
            return parent::renderLayout($output);
        } else {
            return $this->___callPlugins('renderLayout', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setIsLayoutLoaded($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setIsLayoutLoaded');
        if (!$pluginInfo) {
            return parent::setIsLayoutLoaded($value);
        } else {
            return $this->___callPlugins('setIsLayoutLoaded', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isLayoutLoaded()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isLayoutLoaded');
        if (!$pluginInfo) {
            return parent::isLayoutLoaded();
        } else {
            return $this->___callPlugins('isLayoutLoaded', func_get_args(), $pluginInfo);
        }
    }
}
