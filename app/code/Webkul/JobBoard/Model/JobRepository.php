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

use Magento\Framework\Api\DataObjectHelper;
use Webkul\JobBoard\Model\ResourceModel\Job as ResourceJob;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Webkul\JobBoard\Model\ResourceModel\Job\CollectionFactory as JobCollectionFactory;
use Webkul\JobBoard\Api\Data\JobSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Webkul\JobBoard\Api\JobRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

class JobRepository implements JobRepositoryInterface
{
    /**
     * @var \Webkul\JobBoard\Model\ResourceModel\Job
     */
    protected $resource;

    /**
     * @var \Webkul\JobBoard\Model\JobFactory
     */
    protected $jobFactory;

    /**
     * @var \Webkul\JobBoard\Model\ResourceModel\Job\CollectionFactory
     */
    protected $jobCollectionFactory;

    /**
     * @var \Webkul\JobBoard\Api\Data\JobSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    
    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    
    /**
     * @param ResourceJob $resource
     * @param JobFactory $jobFactory
     * @param JobCollectionFactory $jobCollectionFactory
     * @param JobSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceJob $resource,
        JobFactory $jobFactory,
        JobCollectionFactory $jobCollectionFactory,
        JobSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->jobFactory = $jobFactory;
        $this->jobCollectionFactory = $jobCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Webkul\JobBoard\Api\Data\JobInterface $job
    ) {
        $jobData = $this->extensibleDataObjectConverter->toNestedArray(
            $job,
            [],
            \Webkul\JobBoard\Api\Data\JobInterface::class
        );
        
        $jobModel = $this->jobFactory->create()->setData($jobData);
        
        try {
            $this->resource->save($jobModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the job: %1',
                $exception->getMessage()
            ));
        }
        return $jobModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($jobId)
    {
        $job = $this->jobFactory->create();
        $this->resource->load($job, $jobId);
        if (!$job->getId()) {
            throw new NoSuchEntityException(__('Job with id "%1" does not exist.', $jobId));
        }
        return $job->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $jobboardcollection = $this->jobCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $jobboardcollection,
            \Webkul\JobBoard\Api\Data\JobInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $jobboardcollection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($jobboardcollection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($jobboardcollection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Webkul\JobBoard\Api\Data\JobInterface $job
    ) {
        try {
            $jobModel = $this->jobFactory->create();
            $this->resource->load($jobModel, $job->getJobId());
            $this->resource->delete($jobModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Job: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($jobId)
    {
        return $this->delete($this->getById($jobId));
    }
}
