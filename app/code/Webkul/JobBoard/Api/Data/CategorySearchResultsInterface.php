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

interface CategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Category list.
     * @return \Webkul\JobBoard\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Webkul\JobBoard\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
