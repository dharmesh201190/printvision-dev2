<?php

namespace LR\ArtworkDesign\Model\ResourceModel\ArtworkDesign;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'artworkdesign_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'LR\ArtworkDesign\Model\ArtworkDesign',
            'LR\ArtworkDesign\Model\ResourceModel\ArtworkDesign'
        );
    }
}