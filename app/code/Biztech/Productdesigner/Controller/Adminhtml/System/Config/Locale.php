<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Locale extends Action {

    protected $_dir;
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_helper;
    protected $resultJsonFactory;
    protected $logger;
    protected $_themeProvider;

    public function __construct(
        Context $context, \Magento\Framework\Filesystem\DirectoryList $dir, \Biztech\Productdesigner\Helper\Data $helper, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Psr\Log\LoggerInterface $logger, \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_dir = $dir;
        $this->logger = $logger;
        $this->_themeProvider = $themeProvider;
        parent::__construct($context);
    }

    public function execute() {
        try {
            $localePath = $this->_dir->getRoot() . '/app/code/Biztech/Productdesigner/view/frontend/web/dist/assets/i18n/';
            $themeId = $this->_scopeConfig->getValue(
                \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId()
            );            
            $theme = $this->_themeProvider->getThemeById($themeId);
            $themePath = $theme->getThemePath();            
            $themePath = ($themePath) ? $themePath : 'Magento/luma';
            if (!file_exists($localePath)) {
                mkdir($localePath, 0777, true);
            }
            $stores = $this->_storeManager->getStores();
            foreach ($stores as $store) {
                $localeCode = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
                $localeStaticPath[] = $this->_dir->getRoot() . '/pub/static/frontend/' . $themePath . '/' . $localeCode . '/Biztech_Productdesigner/dist/assets/i18n/';
                $storelang[] = $localeCode;
            }
            foreach ($localeStaticPath as $staticPath) {
                if (!file_exists($staticPath)) {
                    mkdir($staticPath, 0777, true);
                }
            }
            $count = 0;
            foreach ($storelang as $lang) {
                $lang_code = explode("_", $lang);
                $localeFile = $localePath . $lang_code[0] . '.json';
                $localeStaticFile = $localeStaticPath[$count] . $lang_code[0] . '.json';
                $count++;
                $file = $this->_dir->getRoot() . '/app/code/Biztech/Productdesigner/i18n/' . $lang . '.csv';

                // $csv = file_get_contents($file);
                $csv = $this->prepareCsvData($lang . ".csv");
                $csvArray = array_map("str_getcsv", explode("\n", $csv));
                $html = '{' . PHP_EOL;
                foreach ($csvArray as $key => $value) {
                    if (isset($value[1])) {
                        $translationArray = explode("%s", $value[1]);
                    } else {
                        $translationArray = [];
                    }
                    $translationString = '';
                    if (count($translationArray) > 1) {
                        for ($i = 1; $i < count($translationArray); $i++) {
                            $translationString .= $translationArray[$i - 1];
                            $translationString .= "{{attr" . $i . "}}";
                        }
                        $translationString .= $translationArray[$i - 1];
                    } else {
                        $translationString = isset($value[1]) ? $value[1] : '';
                    }
                    if ($key + 1 != count($csvArray)) {
                       $value[0] = isset($value[0]) ? $value[0] : '';
                       if (strpos($value[0], '"') !== false && strpos($value[0], "'") !== false) {
                        $start = strpos($value[0], '"');
                        $end = strpos($value[0], '"', $start + 1);

                        $string1 = substr($value[0], $start + 1, $end - $start - 1);
                        $string1 = '"' . $string1 . '"';
                        $string2 = addslashes($string1);
                        $string3 = str_replace($string1, $string2, $value[0]);
                        $html = $html . '"' . trim($string3) . '"' . ':' . '"' . trim($string3) . '"' . ',' . PHP_EOL;

                    } else if (strpos($value[0], '\'') === false) {
                        $html = $html . '"' . trim(addslashes($value[0])) . '"' . ':' . '"' .trim(addslashes($translationString)) . '"' . ',' . PHP_EOL;
                    } else {
                        $html = $html . '"' . trim($value[0]) . '"' . ':' . '"' . trim($translationString) . '"' . ',' . PHP_EOL;
                    }
                } else {
                    if (!isset($value[0]) || !isset($value[1])) {
                        $html = $html . '" "' . ':' . '" "';
                    } else {
                        if (strpos($value[0], '\'') === false) {
                            $html = $html . '"' . trim(addslashes($value[0])) . '"' . ':' . '"' . trim(addslashes($translationString)) . '"' . ',' . PHP_EOL;
                        } else {
                            $html = $html . '"' . trim($value[0]) . '"' . ':' . '"' . trim($translationString) . '"' . ',' . PHP_EOL;
                        }
                    }
                }
            }
            $html = $html . '}';
            file_put_contents($localeFile, $html);
            file_put_contents($localeStaticFile, $html);
        }
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => true]);
    } catch (\Exception $e) {
        $this->logger->critical($e);
    }
}

public function prepareCsvData($lang) {
    $path = $this->_dir->getRoot() . '/app/code/Biztech/*';
    $modules = glob($path);
    $content = '';
    foreach($modules as $module) {
        $i18n = $module . "/i18n";
        if(is_dir($i18n)) {
            $i18n .= "/" . $lang;
            if(file_exists($i18n)) {
                $content .= file_get_contents($i18n);                    
            }
        }
    }
    return $content;
}

protected function _isAllowed() {
    return $this->_authorization->isAllowed('Biztech_Productdesigner::config');
}

}
