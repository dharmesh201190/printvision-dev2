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

interface ApplicationRepositoryInterface
{

    /**
     * Save Application
     * @param \Webkul\JobBoard\Api\Data\ApplicationInterface $application
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Webkul\JobBoard\Api\Data\ApplicationInterface $application
    );

    /**
     * Retrieve Application
     * @param string $applicationId
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($applicationId);

    /**
     * Retrieve Application matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Webkul\JobBoard\Api\Data\ApplicationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Application
     * @param \Webkul\JobBoard\Api\Data\ApplicationInterface $application
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\JobBoard\Api\Data\ApplicationInterface $application
    );

    /**
     * Delete Application by ID
     * @param string $applicationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($applicationId);
}
