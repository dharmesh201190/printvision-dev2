<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Block\Adminhtml\Config\Form\Renderer;

use Biztech\Magemobcart\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Encryption\EncryptorInterface;

class Website extends Field
{
    protected $scopeConfig;
    protected $helper;
    protected $encrypt;
    protected $storeManager;

    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        Data $helper,
        array $data = []
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_encrypt = $encryptor;
        $this->_helper = $helper;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * @param  AbstractElement $element
     * @return mixed
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '';
        $data = $this->_scopeConfig->getValue('magemobcart/activation/data');
        $eleValue = explode(',', str_replace($data, '', $this->_encrypt->decrypt($element->getValue())));
        $eleName = $element->getName();
        $eleId = $element->getId();
        $element->setName($eleName . '[]');
        $dataInfo1 = $this->_helper->getDataInfo();
        $dataInfo = (array)$dataInfo1;
        if (isset($dataInfo['dom']) && intval($dataInfo['c']) > 0 && intval($dataInfo['suc']) == 1) {
            foreach ($this->_storeManager->getWebsites() as $website) {
                $url = $this->_scopeConfig->getValue('web/unsecure/base_url');
                $url = $this->_helper->getFormatUrl(trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url)));
                foreach ($dataInfo['dom'] as $web) {
                    if ($web->dom == $url && $web->suc == 1) {
                        $element->setChecked(false);
                        $id = $website->getId();
                        $name = $website->getName();
                        $element->setId($eleId . '_' . $id);
                        $element->setValue($id);
                        if (in_array($id, $eleValue) !== false) {
                            $element->setChecked(true);
                        }
                        if ($id != 0) {
                            $html .= '<div><label>' . $element->getElementHtml() . ' ' . $name . ' </label></div>';
                        }
                    }
                }
            }
        } else {
            $html = sprintf('<strong class="required" style="color:red">%s</strong>', __('Please enter a valid keywebsite'));
        }
        return $html;
    }
}
