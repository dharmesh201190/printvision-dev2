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
namespace Webkul\JobBoard\Model\Application;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Webkul\JobBoard\Helper\Data;
use Webkul\JobBoard\Model\ResourceModel\Application\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Array
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Webkul\JobBoard\Helper\Data
     */
    protected $helper;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param FileSystem $fileSystem
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        FileSystem $fileSystem,
        Data $helper,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->fileSystem = $fileSystem;
        $this->helper = $helper;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
 
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        
        $applications = $this->collection->getItems();
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $path = $mediaDirectory->getAbsolutePath("jobboard/resume/");
        $mediaUrl = $this->helper->getMediaUrl()."jobboard/application/";
        foreach ($applications as $application) {
            $applicationData = $application->getData();
            $resume = [];
            $resume[0]['image'] = $applicationData['resume'];
            $resume[0]['name'] = $applicationData['resume'];
            $resume[0]['url'] = $mediaUrl.$applicationData['resume'];
            $applicationData['resume'] = $resume;
            $this->loadedData[$application->getId()] = $applicationData;
        }
        return $this->loadedData;
    }
}
