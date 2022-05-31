<?php

namespace Biztech\Magemobcart\Model\System\Config;

class Orderstatusmessage
{
    protected $orderStatusCollection;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\Collection $orderStatusCollection
    ) {
        $this->_orderStatusCollection = $orderStatusCollection;
    }
    public function toOptionArray()
    {
        $data = array();
        $status_data = $this->_orderStatusCollection;
        foreach ($status_data as $status) {
            $data[$status->getStatus()] = 'Order status has been changed to '.$status->getLabel();
        }
        return $data;
    }
    
    public function toOrderOptionArray()
    {
        $status_data = $this->_orderStatusCollection;
        $data = array();
        foreach ($status_data as $status) {
            $data[$status->getStatus()] = 'Order status is '.$status->getLabel();
        }
        return $data;
    }
}
