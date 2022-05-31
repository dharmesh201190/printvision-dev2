<?php

namespace Biztech\Magemobcart\Model\System\Config;

class Orderstatus
{
    protected $orderStatusCollection;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\Collection $orderStatusCollection
    ) {
        $this->_orderStatusCollection = $orderStatusCollection;
    }
    public function toOptionArray()
    {
        $statusCollection = $this->_orderStatusCollection;
        return $statusCollection->toOptionArray();
    }
}
