<?php

namespace Biztech\Productdesigner\Observer;

use Magento\Framework\Event\ObserverInterface;

class InitOrderDesign implements ObserverInterface {

    protected $designOrderFactory;
    protected $_eventManager;
    protected $design;

    public function __construct(
      \Biztech\Productdesigner\Model\DesignOrdersFactory $designOrderFactory, \Magento\Framework\Event\Manager $manager,\Biztech\Productdesigner\Model\DesignsFactory $design
    ) {
        $this->designOrderFactory = $designOrderFactory;
        $this->_eventManager = $manager;
        $this->design = $design;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $order_id = $order->getIncrementId();

        try {
            foreach ($order->getAllItems() as $item) {
                $itemID = $item->getQuoteItemId();
                $product_design = false;
                if (isset($item->getProductOptions()['additional_options'])) {
                    foreach ($item->getProductOptions()['additional_options'] as $additional) {
                        if (isset($additional['code']))
                        if ($additional['code'] == 'product_design') {
                            $product_design = true;
                            $design_id = $additional['design_id'];
                        }
                    }
                    if ($product_design) {
                        $designModel = $this->designOrderFactory->create();
                        $designModel->setOrderId($order_id)
                                ->setDesignId($design_id)
                                ->setItemId($itemID)
                                ->setStatus(0)
                                ->save();

                        // changes by aj set is display flag to 1
                        $model = $this->design->create()->load($design_id);
                        $model->setIsDisplay(1);
                        $model->save();
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

}
