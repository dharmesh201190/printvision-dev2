<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_JobBoard
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\JobBoard\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Webkul\JobBoard\Api\Data\CategoryInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Webkul\JobBoard\Model\ResourceModel\Category as ResourceCategory;
use Webkul\JobBoard\Model\ResourceModel\Category\Collection;
use Webkul\JobBoard\Api\Data\CategoryInterface;

class Category extends AbstractModel
{
    /**
     * @var \Webkul\JobBoard\Api\Data\CategoryInterfaceFactory
     */
    protected $categoryDataFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    
    /**
     * @var string $_eventPrefix
     */
    protected $_eventPrefix = 'jobboard_category';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CategoryInterfaceFactory $categoryDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceCategory $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryInterfaceFactory $categoryDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceCategory $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->categoryDataFactory = $categoryDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve category model with category data
     * @return CategoryInterface
     */
    public function getDataModel()
    {
        $categoryData = $this->getData();
        
        $categoryDataObject = $this->categoryDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $categoryDataObject,
            $categoryData,
            CategoryInterface::class
        );
        
        return $categoryDataObject;
    }
}
