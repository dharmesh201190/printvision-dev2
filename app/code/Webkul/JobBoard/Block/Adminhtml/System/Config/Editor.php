<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_JobBoard
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\JobBoard\Block\Adminhtml\System\Config;
  
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;
  
class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @param Context       $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array         $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }
  
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setWysiwyg(true);

        $config = $this->_wysiwygConfig->getConfig($element);
        $config->setAddVariables(false);
        $config->setAddWidgets(false);
        foreach ($config as $key) {
            if (array_key_exists("widget_placeholders", $key)) {
                unset($config['widget_placeholders']);
                unset($config['widget_plugin_src']);
            };
        }
        $plugins = $config->getPlugins();
        //foreach ($plugins as $key => $plug) {
           // if ($plug['name'] == "magentovariable" || $plug['name'] == "magentowidget") {
           //     unset($plugins[$key]);
           // };
        //}
        $config->setPlugins($plugins);
        $element->setConfig($config);
        return parent::_getElementHtml($element);
    }
}
