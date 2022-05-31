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
namespace Webkul\JobBoard\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Webkul\JobBoard\Model\JobFactory;

class JobName extends Column
{
    /**
     * @var \Webkul\JobBoard\Model\JobFactory
     */
    protected $jobFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param JobFactory $jobFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        JobFactory $jobFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->jobFactory = $jobFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $fieldName = $this->getData('name');
                $jobName = $this->getDesignationName($item[$fieldName]);
                $item[$fieldName] = $jobName;
            }
        }
        return $dataSource;
    }

    /**
     * Get Job Designation from Job Id
     *
     * @param Int $jobId
     *
     * @return String Job Designation
     */
    public function getDesignationName($jobId)
    {
        return $this->jobFactory->create()->load($jobId)->getDesignation();
    }
}
