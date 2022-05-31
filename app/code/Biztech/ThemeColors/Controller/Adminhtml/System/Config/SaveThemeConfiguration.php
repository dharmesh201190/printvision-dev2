<?php
namespace Biztech\ThemeColors\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use \Leafo\ScssPhp\Compiler;


class SaveThemeConfiguration extends Action {

    protected $_scopeConfig;
    protected $_layoutInterface;
    protected $_themeProvider;
    protected $moduleReader;
    protected $_dir;
    protected $resultJsonFactory;
    protected $logger;


    public function __construct(
       \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\View\LayoutInterface $layoutInterface, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider, \Magento\Framework\Module\Dir\Reader $moduleReader, \Magento\Framework\Filesystem\DirectoryList $dir,Context $context, JsonFactory $resultJsonFactory, \Psr\Log\LoggerInterface $logger
   ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_layoutInterface = $layoutInterface;
        $this->_storeManager = $storeManager;
        $this->_themeProvider = $themeProvider;
        $this->moduleReader = $moduleReader;
        $this->_dir = $dir;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute() {
        try{
            $primaryColor = $this->getRequest()->getParam('themecolor');
            $themeType= $this->getRequest()->getParam('themetype');
            $currentStore = $this->getRequest()->getParam('store');
            $resultPage = $this->_layoutInterface->createBlock('Biztech\ThemeColors\Block\Themecolors');
            $css = $resultPage->setData(array("primary_background" => $primaryColor,"theme_type" => $themeType))->setTemplate('themecolors/system/config/theme.phtml')->toHtml();
            $themeId = $this->_scopeConfig->getValue(
                \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
            $theme = $this->_themeProvider->getThemeById($themeId);
            $viewDir = $this->moduleReader->getModuleDir(
                \Magento\Framework\Module\Dir::MODULE_VIEW_DIR, 'Biztech_Productdesigner');
            $cssDir = $this->moduleReader->getModuleDir(
                \Magento\Framework\Module\Dir::MODULE_VIEW_DIR, 'Biztech_ThemeColors');
            $cssfile = $cssDir . '/frontend/web/dist/assets/sass/theme-switch.scss';
            file_put_contents($cssfile, $css);
            $scss = new Compiler();
            $scss->setImportPaths($cssDir . "/frontend/web/dist");
            $allCSSContent = '';
            $allCSSContent .= file_get_contents($cssDir."/frontend/web/dist/bootstrap.min.css");
            $allCSSContent .= $scss->compile('@import "styles.scss"');
            $allCSSContent .= file_get_contents($cssDir."/frontend/web/dist/cropper.css");
            $allCSSContent .= file_get_contents($cssDir."/frontend/web/dist/introjs.min.css");

            $currentcssFile = $viewDir . "/frontend/web/dist/styles". $currentStore.".css";

            file_put_contents($currentcssFile, $allCSSContent);
            $stores = $this->_storeManager->getStores();
            $minifiedCSSConfiguration = $this->_scopeConfig->getValue('dev/css/minify_files', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            foreach ($stores as $store) {
                $localeCode = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
                $path = $theme->getThemePath();
                if($path)
                {
                    $newpath=$path;
                }
                else{
                    $newpath='Magento/luma';
                }
                $staticPath = $this->_dir->getPath('pub') . '/static/frontend/' . $newpath . '/' . $localeCode . '/Biztech_Productdesigner/dist';
                if ($minifiedCSSConfiguration) {
                    $currentStaticcssFile = $staticPath . '/styles'.$currentStore.'.min.css';
                } else {
                    $currentStaticcssFile = $staticPath . '/styles'.$currentStore.'.css';
                }
                if (!file_exists($staticPath)) {
                    mkdir($staticPath, 0777, true);
                }
                file_put_contents($currentStaticcssFile, $allCSSContent);
            }
            $result = $this->resultJsonFactory->create();
            return $result->setData(['success' => true, 'themecolor' => 'Done']);
        }catch(\Exception $e){
            $this->logger->critical($e);
            $result = $this->resultJsonFactory->create();
            return $result->setData(['success' => false, 'themecolor' => $e->getMessage()]);
        }
    }
}
