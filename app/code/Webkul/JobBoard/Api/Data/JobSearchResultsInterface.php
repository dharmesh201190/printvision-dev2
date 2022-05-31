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
namespace Webkul\JobBoard\Api\Data;

interface JobSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Job list.
     * @return \Webkul\JobBoard\Api\Data\JobInterface[]
     */
    public function getItems();

    /**
     * Set designation list.
     * @param \Webkul\JobBoard\Api\Data\JobInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
