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
use Webkul\JobBoard\Api\Data\JobInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Webkul\JobBoard\Model\ResourceModel\Job as ResourceJob;
use Webkul\JobBoard\Model\ResourceModel\Job\Collection;
use Webkul\JobBoard\Api\Data\JobInterface;

class Job extends AbstractModel
{
    /**
     * @var \Webkul\JobBoard\Api\Data\JobInterfaceFactory
     */
    protected $jobDataFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    
    /**
     * @var string $_eventPrefix
     */
    protected $_eventPrefix = 'jobboard_job';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param JobInterfaceFactory $jobDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceJob $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        JobInterfaceFactory $jobDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceJob $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->jobDataFactory = $jobDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve job model with job data
     * @return JobInterface
     */
    public function getDataModel()
    {
        $jobData = $this->getData();
        
        $jobDataObject = $this->jobDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $jobDataObject,
            $jobData,
            JobInterface::class
        );
        
        return $jobDataObject;
    }
}
