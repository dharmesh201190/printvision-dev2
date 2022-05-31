<?php
namespace Magento\Framework\Config\View;

/**
 * Interceptor class for @see \Magento\Framework\Config\View
 */
class Interceptor extends \Magento\Framework\Config\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Config\FileResolverInterface $fileResolver, \Magento\Framework\Config\ConverterInterface $converter, \Magento\Framework\Config\SchemaLocatorInterface $schemaLocator, \Magento\Framework\Config\ValidationStateInterface $validationState, $fileName, $idAttributes = [], $domDocumentClass = 'Magento\\Framework\\Config\\Dom', $defaultScope = 'global', $xpath = [])
    {
        $this->___init();
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass, $defaultScope, $xpath);
    }

    /**
     * {@inheritdoc}
     */
    public function getVars($module)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVars');
        if (!$pluginInfo) {
            return parent::getVars($module);
        } else {
            return $this->___callPlugins('getVars', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVarValue($module, $var)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVarValue');
        if (!$pluginInfo) {
            return parent::getVarValue($module, $var);
        } else {
            return $this->___callPlugins('getVarValue', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaEntities($module, $mediaType)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMediaEntities');
        if (!$pluginInfo) {
            return parent::getMediaEntities($module, $mediaType);
        } else {
            return $this->___callPlugins('getMediaEntities', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaAttributes($module, $mediaType, $mediaId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMediaAttributes');
        if (!$pluginInfo) {
            return parent::getMediaAttributes($module, $mediaType, $mediaId);
        } else {
            return $this->___callPlugins('getMediaAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExcludedFiles()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExcludedFiles');
        if (!$pluginInfo) {
            return parent::getExcludedFiles();
        } else {
            return $this->___callPlugins('getExcludedFiles', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExcludedDir()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExcludedDir');
        if (!$pluginInfo) {
            return parent::getExcludedDir();
        } else {
            return $this->___callPlugins('getExcludedDir', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read($scope = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'read');
        if (!$pluginInfo) {
            return parent::read($scope);
        } else {
            return $this->___callPlugins('read', func_get_args(), $pluginInfo);
        }
    }
}
