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
namespace Webkul\JobBoard\Model\Job;
 
use Webkul\JobBoard\Model\ResourceModel\Job\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
 
class DataProvider extends AbstractDataProvider
{
    /**
     * @var Array
     */
    protected $_loadedData;

    /**
     * @param CollectionFactory $collectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $jobboardCollectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $jobboardCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
 
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        
        $jobs = $this->collection->getItems();
        foreach ($jobs as $job) {
            $this->_loadedData[$job->getId()] = $job->getData();
        }

        return $this->_loadedData;
    }
}
