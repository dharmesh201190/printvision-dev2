<?php

namespace SamSolutions\Artwork\Cron;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class CheckOrderOnArtwork
{

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    public function __construct(
        OrderRepository $orderRepository,
        CollectionFactory $orderFactory

    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
    }

    public function execute()
    {
        $orders = $this->orderFactory->create()->addFieldToSelect('*')->addFieldToFilter('is_needed_artwork',
            ['null' => true]
        );
        foreach ($orders as $order) {
            $order->setIsNeededArtwork(2);
            foreach ($order->getItems() as $item) {
                if (isset($item->getProductOptions()['additional_options'])) {
                    if (is_array($item->getProductOptions()['additional_options'])) {
                        foreach ($item->getProductOptions()['additional_options'] as $key => $option) {
                            if (is_array($option)) {
                                if ($option["label"] == "Artwork After") {
                                    $order->setIsNeededArtwork(1);
                                    $this->orderRepository->save($order);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }
}
