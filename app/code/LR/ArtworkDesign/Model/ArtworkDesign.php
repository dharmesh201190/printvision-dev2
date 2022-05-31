<?php

namespace LR\ArtworkDesign\Model;

use Magento\Framework\Model\AbstractModel;

class ArtworkDesign extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('LR\ArtworkDesign\Model\ResourceModel\ArtworkDesign');
    }
}