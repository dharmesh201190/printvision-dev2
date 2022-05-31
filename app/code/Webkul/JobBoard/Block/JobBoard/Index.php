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
namespace Webkul\JobBoard\Block\JobBoard;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Customer\Model\Session as CustomerSession;
use Webkul\JobBoard\Helper\Data;
use Webkul\JobBoard\Model\ResourceModel\Job\CollectionFactory as JobCollectionFactory;
use Webkul\JobBoard\Model\ResourceModel\Category\CollectionFactory as JobCategoryCollection;

class Index extends Template
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Webkul\JobBoard\Helper\Data
     */
    protected $helper;

    /**
     * @var \Webkul\JobBoard\Model\ResourceModel\Job\CollectionFactory
     */
    protected $jobCollectionFactory;

    /**
     * @var \Webkul\JobBoard\Model\ResourceModel\Category\CollectionFactory
     */
    protected $jobCategoryCollection;

    /**
     * @param Context $context
     * @param Json $jsonSerializer
     * @param CustomerSession $customerSession
     * @param Data $helper
     * @param JobCollectionFactory $jobCollectionFactory
     * @param JobCategoryCollection $jobCategoryCollection
     */
    public function __construct(
        Context $context,
        Json $jsonSerializer,
        CustomerSession $customerSession,
        Data $helper,
        JobCollectionFactory $jobCollectionFactory,
        JobCategoryCollection $jobCategoryCollection,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->jsonSerializer = $jsonSerializer;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->jobCollectionFactory = $jobCollectionFactory;
        $this->jobCategoryCollection = $jobCategoryCollection;
        $this->_filterProvider = $filterProvider;
        $this->_pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Return Json Serializer
     *
     * @return Object
     */
    public function getJsonSerializer()
    {
        return $this->jsonSerializer;
    }

    /**
     * Return JobBoard Helper
     *
     * @return Object
     */
    public function getJobBoardHelper()
    {
        return $this->helper;
    }

    /**
     * Return Customer Session MOdel
     *
     * @return Object
     */
    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * Get Enabled Job Categories Collection
     *
     * @return Collection $jobCategories
     */
    public function getJobCategories()
    {
        $jobCategories = $this->jobCategoryCollection->create()
                              ->addFieldToFilter("status", 1)
                              ->setOrder('sort', 'ASC');
        return $jobCategories;
    }

    /**
     * Get Enabled Job Collection for specific Category
     *
     * @param Int $jobCategory
     *
     * @return Collection $jobCollection
     */
    public function getJobCollection($jobCategoryId)
    {
        $jobCollection = $this->jobCollectionFactory->create()
                              ->addFieldToFilter("category", $jobCategoryId)
                              ->addFieldToFilter("status", 1);
        return $jobCollection;
    }

    /**
     * Get Form Submit Action URL
     *
     * @return String
     */
    public function getFormAction()
    {
        return $this->getUrl('jobboard/index/apply');
    }

    /**
     * Return Job Collection Data for View Job Details
     *
     * @return Array
     */
    public function getJobCollectionData()
    {
        $jobData = [];
        $jobCategories = $this->getJobCategories();
        foreach ($jobCategories as $jobCategory) {
            $jobCollection = $this->getJobCollection($jobCategory->getId());
            foreach ($jobCollection as $job) {
                $jobData[$job->getId()] = [
                    'name'=> $job->getDesignation(),
                    'description' => $job->getDescription(),
                    'salary' => $job->getSalary(),
                    'eligibility'=> $job->getEligibility(),
                    'skills' => $job->getSkills(),
                    'location' => $job->getLocation()
                ];
            }
        }

        return $jobData;
    }

    /**
     * Return Customer Field Value
     *
     * @param String $field
     * @return String
     */
    public function getCustomerData($field)
    {
        return $this->customerSession->getCustomer()->getData($field);
    }

    /**
     * Return Customer Address
     *
     * @return String
     */
    public function getCustomerAddress()
    {
        $addressData = "";
        $address = $this->customerSession->getCustomer()->getDefaultBillingAddress();
        if ($address) {
            $addressData = $address->getStreetFull()."\n";
            $addressData .= $address->getData("city")."\n";
            $addressData .= $address->getRegion()."\n";
            $addressData .= $address->getCountry();
        }
        return $addressData;
    } 

    public function getPageContent($content) {

        $html = $this->_filterProvider->getPageFilter()->filter($content);
        return $html;
    }
}
