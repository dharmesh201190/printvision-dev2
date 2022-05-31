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

use Magento\Framework\Api\ExtensibleDataInterface;

interface CategoryInterface extends ExtensibleDataInterface
{
    /**
     * @var String
     */
    const ENTITY_ID = 'id';

    /**
     * @var String
     */
    const NAME = 'name';
    
    /**
     * @var String
     */
    const SORT = 'sort';

    /**
     * @var String
     */
    const STATUS = 'status';

    /**
     * @var String
     */
    const CREATED_AT = 'created_at';

    /**
     * @var String
     */
    const UPDATED_AT = 'updated_at';
    
    /**
     * Get Id
     * @return string|null
     */
    public function getId();

    /**
     * Set Id
     * @param string $id
     * @return \Webkul\JobBoard\Api\Data\CategoryInterface
     */
    public function setId($id);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Webkul\JobBoard\Api\Data\CategoryInterface
     */
    public function setName($name);

    /**
     * Get sort
     * @return string|null
     */
    public function getSort();

    /**
     * Set sort
     * @param string $sort
     * @return \Webkul\JobBoard\Api\Data\CategoryInterface
     */
    public function setSort($sort);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Webkul\JobBoard\Api\Data\CategoryInterface
     */
    public function setStatus($status);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Webkul\JobBoard\Api\Data\CategoryInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Webkul\JobBoard\Api\Data\CategoryInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\JobBoard\Api\Data\CategoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\JobBoard\Api\Data\CategoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\JobBoard\Api\Data\CategoryExtensionInterface $extensionAttributes
    );
}
