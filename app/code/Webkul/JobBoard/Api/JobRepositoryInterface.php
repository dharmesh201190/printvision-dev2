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
namespace Webkul\JobBoard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface JobRepositoryInterface
{

    /**
     * Save Job
     * @param \Webkul\JobBoard\Api\Data\JobInterface $job
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Webkul\JobBoard\Api\Data\JobInterface $job
    );

    /**
     * Retrieve Job
     * @param string $jobId
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($jobId);

    /**
     * Retrieve Job matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Webkul\JobBoard\Api\Data\JobSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Job
     * @param \Webkul\JobBoard\Api\Data\JobInterface $job
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\JobBoard\Api\Data\JobInterface $job
    );

    /**
     * Delete Job by ID
     * @param string $jobId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($jobId);
}
