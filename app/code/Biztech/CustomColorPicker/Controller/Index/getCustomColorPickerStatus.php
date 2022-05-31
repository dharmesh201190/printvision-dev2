<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\CustomColorPicker\Controller\Index;

class getCustomColorPickerStatus extends \Magento\Framework\App\Action\Action
{
	protected $_storeManager;
	protected $_helper;
	protected $_logger;
	/**
	 * Index action
	 *
	 * @return $this
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\CustomColorPicker\Helper\Data $helper, \Psr\Log\LoggerInterface $logger
	) {
		$this->_storeManager = $storeManager;
		$this->_helper = $helper;
		$this->_logger = $logger;
		parent::__construct($context);
	}

	public function execute(){
        try {
			$storeid = $this->_storeManager->getStore()->getId();
			$colorPickerConfiguration = array();
			$colorPickerConfiguration['customcolorpickerstatus'] = $this->_helper->getConfig('productdesigner/customcolorpicker/customcolorpickerenable', $storeid);
			$colorPickerConfiguration['cmykenable'] = $this->_helper->getConfig('productdesigner/manage_cmyk/cmyk_enable', $storeid);
			$colorPickerConfiguration['flag'] = 'true';
			$this->getResponse()->setBody(json_encode($colorPickerConfiguration));
        } catch(\Exception $e) {
			$this->_logger->critical($e->getMessage());
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/colorpicker.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $text = "getComponentConfig: " . $component . " - " . $e->getMessage();
            $logger->info($text);
		}
    }
}
