<?php

namespace Biztech\Productdesigner\Observer;

use Magento\Framework\Event\ObserverInterface;

class salesConvert implements ObserverInterface {

    protected $_request;
    protected $_bizHelper;
    protected $cartModel;

    public function __construct(
    \Magento\Framework\App\Request\Http $request, \Biztech\Productdesigner\Helper\Data $bizHelper, \Magento\Checkout\Model\CartFactory $cartModel
    ) {
        $this->_request = $request;
        $this->_bizHelper = $bizHelper;
        $this->cartModel = $cartModel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $orderItems = $observer->getOrder()->getAllItems();
        $carts = $this->cartModel->create()->getQuote();
        $i = 0;

        foreach ($carts->getAllItems() as $item) {
            if ($additionalOptions = $item->getOptionByCode('additional_options')) {
                $options = $orderItems[$i]->getProductOptions();
                $options['additional_options'] = $this->_bizHelper->unserializeData($additionalOptions->getValue());
                $orderItems[$i]->setProductOptions($options);
            }
            $i++;
        }
    }

}
