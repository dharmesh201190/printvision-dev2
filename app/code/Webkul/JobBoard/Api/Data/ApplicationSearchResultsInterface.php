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

interface ApplicationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Application list.
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface[]
     */
    public function getItems();

    /**
     * Set job list.
     * @param \Webkul\JobBoard\Api\Data\ApplicationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
