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

use Webkul\JobBoard\Model\ResourceModel\Application as ResourceApplication;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Webkul\JobBoard\Api\Data\ApplicationSearchResultsInterfaceFactory;
use Webkul\JobBoard\Model\ResourceModel\Application\CollectionFactory as ApplicationCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Webkul\JobBoard\Api\ApplicationRepositoryInterface;
use Webkul\JobBoard\Api\Data\ApplicationInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;

class ApplicationRepository implements ApplicationRepositoryInterface
{
    /**
     * @var \Webkul\JobBoard\Model\ResourceModel\Application
     */
    protected $resource;

    /**
     * @var \Webkul\JobBoard\Model\ApplicationFactory
     */
    protected $applicationFactory;
    
    /**
     * @var \Webkul\JobBoard\Api\Data\ApplicationInterfaceFactory
     */
    protected $dataApplicationFactory;

    /**
     * @var \Webkul\JobBoard\Model\ResourceModel\Application\CollectionFactory
     */
    protected $applicationCollectionFactory;

    /**
     * @var \Webkul\JobBoard\Api\Data\ApplicationSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

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
     * @param ResourceApplication $resource
     * @param ApplicationFactory $applicationFactory
     * @param ApplicationInterfaceFactory $dataApplicationFactory
     * @param ApplicationCollectionFactory $applicationCollectionFactory
     * @param ApplicationSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceApplication $resource,
        ApplicationFactory $applicationFactory,
        ApplicationInterfaceFactory $dataApplicationFactory,
        ApplicationCollectionFactory $applicationCollectionFactory,
        ApplicationSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->applicationFactory = $applicationFactory;
        $this->applicationCollectionFactory = $applicationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataApplicationFactory = $dataApplicationFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Webkul\JobBoard\Api\Data\ApplicationInterface $application
    ) {
        $applicationData = $this->extensibleDataObjectConverter->toNestedArray(
            $application,
            [],
            \Webkul\JobBoard\Api\Data\ApplicationInterface::class
        );
        
        $applicationModel = $this->applicationFactory->create()->setData($applicationData);
        
        try {
            $this->resource->save($applicationModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the application: %1',
                $exception->getMessage()
            ));
        }
        return $applicationModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($applicationId)
    {
        $application = $this->applicationFactory->create();
        $this->resource->load($application, $applicationId);
        if (!$application->getId()) {
            throw new NoSuchEntityException(__('Application with id "%1" does not exist.', $applicationId));
        }
        return $application->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->applicationCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Webkul\JobBoard\Api\Data\ApplicationInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Webkul\JobBoard\Api\Data\ApplicationInterface $application
    ) {
        try {
            $applicationModel = $this->applicationFactory->create();
            $this->resource->load($applicationModel, $application->getApplicationId());
            $this->resource->delete($applicationModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Application: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($applicationId)
    {
        return $this->delete($this->getById($applicationId));
    }
}
