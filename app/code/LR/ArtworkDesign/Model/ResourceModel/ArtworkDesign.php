<?php

namespace LR\ArtworkDesign\Model\ResourceModel;

class ArtworkDesign extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('lr_artworkdesign', 'artworkdesign_id');   //here "lr_artworkdesign" is table name and "artworkdesign_id" is the primary key of custom table
    }
}