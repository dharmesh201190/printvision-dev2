<?php
namespace Magento\Cms\Helper\Wysiwyg\Images;

/**
 * Interceptor class for @see \Magento\Cms\Helper\Wysiwyg\Images
 */
class Interceptor extends \Magento\Cms\Helper\Wysiwyg\Images implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Backend\Helper\Data $backendData, \Magento\Framework\Filesystem $filesystem, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Escaper $escaper)
    {
        $this->___init();
        parent::__construct($context, $backendData, $filesystem, $storeManager, $escaper);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($store)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setStoreId');
        if (!$pluginInfo) {
            return parent::setStoreId($store);
        } else {
            return $this->___callPlugins('setStoreId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageRoot()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStorageRoot');
        if (!$pluginInfo) {
            return parent::getStorageRoot();
        } else {
            return $this->___callPlugins('getStorageRoot', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageRootSubpath()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStorageRootSubpath');
        if (!$pluginInfo) {
            return parent::getStorageRootSubpath();
        } else {
            return $this->___callPlugins('getStorageRootSubpath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBaseUrl');
        if (!$pluginInfo) {
            return parent::getBaseUrl();
        } else {
            return $this->___callPlugins('getBaseUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTreeNodeName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTreeNodeName');
        if (!$pluginInfo) {
            return parent::getTreeNodeName();
        } else {
            return $this->___callPlugins('getTreeNodeName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertPathToId($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertPathToId');
        if (!$pluginInfo) {
            return parent::convertPathToId($path);
        } else {
            return $this->___callPlugins('convertPathToId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertIdToPath($id)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertIdToPath');
        if (!$pluginInfo) {
            return parent::convertIdToPath($id);
        } else {
            return $this->___callPlugins('convertIdToPath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingStaticUrlsAllowed()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isUsingStaticUrlsAllowed');
        if (!$pluginInfo) {
            return parent::isUsingStaticUrlsAllowed();
        } else {
            return $this->___callPlugins('isUsingStaticUrlsAllowed', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getImageHtmlDeclaration($filename, $renderAsTag = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImageHtmlDeclaration');
        if (!$pluginInfo) {
            return parent::getImageHtmlDeclaration($filename, $renderAsTag);
        } else {
            return $this->___callPlugins('getImageHtmlDeclaration', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPath()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrentPath');
        if (!$pluginInfo) {
            return parent::getCurrentPath();
        } else {
            return $this->___callPlugins('getCurrentPath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrentUrl');
        if (!$pluginInfo) {
            return parent::getCurrentUrl();
        } else {
            return $this->___callPlugins('getCurrentUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function idEncode($string)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'idEncode');
        if (!$pluginInfo) {
            return parent::idEncode($string);
        } else {
            return $this->___callPlugins('idEncode', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function idDecode($string)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'idDecode');
        if (!$pluginInfo) {
            return parent::idDecode($string);
        } else {
            return $this->___callPlugins('idDecode', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getShortFilename($filename, $maxLength = 20)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getShortFilename');
        if (!$pluginInfo) {
            return parent::getShortFilename($filename, $maxLength);
        } else {
            return $this->___callPlugins('getShortFilename', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setImageDirectorySubpath($subpath)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setImageDirectorySubpath');
        if (!$pluginInfo) {
            return parent::setImageDirectorySubpath($subpath);
        } else {
            return $this->___callPlugins('setImageDirectorySubpath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isModuleOutputEnabled');
        if (!$pluginInfo) {
            return parent::isModuleOutputEnabled($moduleName);
        } else {
            return $this->___callPlugins('isModuleOutputEnabled', func_get_args(), $pluginInfo);
        }
    }
}
