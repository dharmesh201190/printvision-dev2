<?php
namespace Magento\Framework\View\Asset\Repository;

/**
 * Interceptor class for @see \Magento\Framework\View\Asset\Repository
 */
class Interceptor extends \Magento\Framework\View\Asset\Repository implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\UrlInterface $baseUrl, \Magento\Framework\View\DesignInterface $design, \Magento\Framework\View\Design\Theme\ListInterface $themeList, \Magento\Framework\View\Asset\Source $assetSource, \Magento\Framework\App\Request\Http $request, \Magento\Framework\View\Asset\FileFactory $fileFactory, \Magento\Framework\View\Asset\File\FallbackContextFactory $fallbackContextFactory, \Magento\Framework\View\Asset\File\ContextFactory $contextFactory, \Magento\Framework\View\Asset\RemoteFactory $remoteFactory)
    {
        $this->___init();
        parent::__construct($baseUrl, $design, $themeList, $assetSource, $request, $fileFactory, $fallbackContextFactory, $contextFactory, $remoteFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function updateDesignParams(array &$params)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'updateDesignParams');
        if (!$pluginInfo) {
            return parent::updateDesignParams($params);
        } else {
            return $this->___callPlugins('updateDesignParams', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createAsset($fileId, array $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createAsset');
        if (!$pluginInfo) {
            return parent::createAsset($fileId, $params);
        } else {
            return $this->___callPlugins('createAsset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticViewFileContext()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStaticViewFileContext');
        if (!$pluginInfo) {
            return parent::getStaticViewFileContext();
        } else {
            return $this->___callPlugins('getStaticViewFileContext', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createSimilar($fileId, \Magento\Framework\View\Asset\LocalInterface $similarTo)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createSimilar');
        if (!$pluginInfo) {
            return parent::createSimilar($fileId, $similarTo);
        } else {
            return $this->___callPlugins('createSimilar', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createArbitrary($filePath, $dirPath, $baseDirType = 'static', $baseUrlType = 'static')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createArbitrary');
        if (!$pluginInfo) {
            return parent::createArbitrary($filePath, $dirPath, $baseDirType, $baseUrlType);
        } else {
            return $this->___callPlugins('createArbitrary', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createRelated($fileId, \Magento\Framework\View\Asset\LocalInterface $relativeTo)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createRelated');
        if (!$pluginInfo) {
            return parent::createRelated($fileId, $relativeTo);
        } else {
            return $this->___callPlugins('createRelated', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createRemoteAsset($url, $contentType)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createRemoteAsset');
        if (!$pluginInfo) {
            return parent::createRemoteAsset($url, $contentType);
        } else {
            return $this->___callPlugins('createRemoteAsset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($fileId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrl');
        if (!$pluginInfo) {
            return parent::getUrl($fileId);
        } else {
            return $this->___callPlugins('getUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlWithParams($fileId, array $params)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrlWithParams');
        if (!$pluginInfo) {
            return parent::getUrlWithParams($fileId, $params);
        } else {
            return $this->___callPlugins('getUrlWithParams', func_get_args(), $pluginInfo);
        }
    }
}
