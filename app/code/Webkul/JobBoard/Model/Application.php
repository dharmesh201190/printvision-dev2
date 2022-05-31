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
use Webkul\JobBoard\Api\Data\ApplicationInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Webkul\JobBoard\Model\ResourceModel\Application as ResourceApplication;
use Webkul\JobBoard\Model\ResourceModel\Application\Collection;

class Application extends AbstractModel
{
    /**
     * @var \Webkul\JobBoard\Api\Data\ApplicationInterfaceFactory
     */
    protected $applicationDataFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    
    /**
     * @var string $_eventPrefix
     */
    protected $_eventPrefix = 'jobboard_application';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ApplicationInterfaceFactory $applicationDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceApplication $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ApplicationInterfaceFactory $applicationDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceApplication $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->applicationDataFactory = $applicationDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve application model with application data
     * @return ApplicationInterface
     */
    public function getDataModel()
    {
        $applicationData = $this->getData();
        
        $applicationDataObject = $this->applicationDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $applicationDataObject,
            $applicationData,
            ApplicationInterface::class
        );
        
        return $applicationDataObject;
    }
}
