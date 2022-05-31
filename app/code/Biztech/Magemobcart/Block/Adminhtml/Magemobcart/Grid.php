<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Block\Adminhtml\Magemobcart;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $bannersliderCollection;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Biztech\Magemobcart\Model\ResourceModel\Magemobcart\Collection $bannersliderCollection,
        array $data = []
    ) {
        $this->_bannersliderCollection = $bannersliderCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('inventorysystemGrid');
        $this->setDefaultSort('magemobcart_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     * @return $this
     */
    protected function _prepareCollection()
    {
        try {
            $collection = $this->_bannersliderCollection;
            $this->setCollection($collection);
            parent::_prepareCollection();
            return $this;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Prepare columns
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'bannerslider_id',
            [
            'header' => __('ID'),
            'index' => 'magemobcart_id',
            'class' => 'entity_id',
            ]
        );
        $this->addColumn(
            'title',
            [
            'header' => __('Title'),
            'index' => 'title',
            'class' => 'entity_id'
                ]
        );
        $this->addColumn(
            'filename',
            [
            'header' => _('Banner Thumbnail'),
            'align' => 'left',
            'index' => 'filename',
            'width' => 97,
            'renderer' => 'Biztech\Magemobcart\Block\Adminhtml\Magemobcart\Renderer\Image',
            'filter' => false,
            'sortable' => false,
                ]
        );
        $this->addColumn(
            'status',
            [
            'header' => __('Status'),
            'class' => 'entity_id',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                2 => 'Disabled',
            )
                ]
        );
        $this->addColumn(
            'sort_order',
            [
            'header' => __('Sort Order'),
            'index' => 'sort_order',
            'class' => 'entity_id'
                ]
        );
        $this->addColumn(
            'action',
            [
            'header' => __('Action'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('Edit'),
                    'url' => ['base' => '*/*/edit'],
                    'field' => 'id',
                ]
            ],
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'header_css_class' => 'col-action',
            'column_css_class' => 'col-action',
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('magemobcart_id');
        $this->getMassactionBlock()->setFormFieldName('magemobcart');

        /* on select of this massaction it will set the values from js */
        $this->getMassactionBlock()->addItem('remove', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massdelete', ['_current' => true]),
            'confirm' => __('Are you sure want to remove selected slide/s?')
        ]);
        $this->getMassactionBlock()->addItem('changestatus', [
            'label' => __('Change Status'),
            'url' => $this->getUrl('*/*/massstatus', ['_current' => true]),
            'confirm' => __('Are you sure want to change selected slider status?'),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => __('Status'),
                    'values' => array(
                        1 => 'Enabled',
                        2 => 'Disabled',
                    )
                )
            )
        ]);
        return $this;
    }
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'magemobcart/*/edit',
            ['id' => $row->getId()]
        );
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
