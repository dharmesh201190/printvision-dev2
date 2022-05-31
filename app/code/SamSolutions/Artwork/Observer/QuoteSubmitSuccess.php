<?php

namespace SamSolutions\Artwork\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use SamSolutions\Artwork\Model\Email;

class QuoteSubmitSuccess implements ObserverInterface
{

    /**
     * @var \SamSolutions\Artwork\Model\Email
     */
    private $email;

    public function __construct(
        Email $email
    ) {
        $this->email = $email;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $items = $order->getItems();
        foreach ($items as $item) {
            if (isset($item->getProductOptions()['additional_options']))
            foreach ($item->getProductOptions()['additional_options'] as $option) {
                if ($option['value'] == 'Yes' || $option['value'] == '1') {
                    $this->email->send($order);

                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}
