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
namespace Webkul\JobBoard\Model\Data;

use Webkul\JobBoard\Api\Data\JobInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Job extends AbstractExtensibleObject implements JobInterface
{

    /**
     * Get id
     * @return string|null
     */
    public function getId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Set id
     * @param string $id
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get designation
     * @return string|null
     */
    public function getDesignation()
    {
        return $this->_get(self::DESIGNATION);
    }

    /**
     * Set designation
     * @param string $designation
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setDesignation($designation)
    {
        return $this->setData(self::DESIGNATION, $designation);
    }

    /**
     * Get category
     * @return string|null
     */
    public function getCategory()
    {
        return $this->_get(self::CATEGORY);
    }

    /**
     * Set category
     * @param string $category
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setCategory($category)
    {
        return $this->setData(self::CATEGORY, $category);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get eligibility
     * @return string|null
     */
    public function getEligibility()
    {
        return $this->_get(self::ELIGIBILITY);
    }

    /**
     * Set eligibility
     * @param string $eligibility
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setEligibility($eligibility)
    {
        return $this->setData(self::ELIGIBILITY, $eligibility);
    }

    /**
     * Get location
     * @return string|null
     */
    public function getLocation()
    {
        return $this->_get(self::LOCATION);
    }

    /**
     * Set location
     * @param string $location
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setLocation($location)
    {
        return $this->setData(self::LOCATION, $location);
    }

    /**
     * Get skills
     * @return string|null
     */
    public function getSkills()
    {
        return $this->_get(self::SKILLS);
    }

    /**
     * Set skills
     * @param string $skills
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setSkills($skills)
    {
        return $this->setData(self::SKILLS, $skills);
    }

    /**
     * Get salary_type
     * @return string|null
     */
    public function getSalaryType()
    {
        return $this->_get(self::SALARY_TYPE);
    }

    /**
     * Set salary_type
     * @param string $salaryType
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setSalaryType($salaryType)
    {
        return $this->setData(self::SALARY_TYPE, $salaryType);
    }

    /**
     * Get salary
     * @return string|null
     */
    public function getSalary()
    {
        return $this->_get(self::SALARY);
    }

    /**
     * Set salary
     * @param string $salary
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setSalary($salary)
    {
        return $this->setData(self::SALARY, $salary);
    }

    /**
     * Get experience
     * @return string|null
     */
    public function getExperience()
    {
        return $this->_get(self::EXPERIENCE);
    }

    /**
     * Set experience
     * @param string $experience
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setExperience($experience)
    {
        return $this->setData(self::EXPERIENCE, $experience);
    }

    /**
     * Get sort
     * @return string|null
     */
    public function getSort()
    {
        return $this->_get(self::SORT);
    }

    /**
     * Set sort
     * @param string $sort
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setSort($sort)
    {
        return $this->setData(self::SORT, $sort);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\JobBoard\Api\Data\JobExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Webkul\JobBoard\Api\Data\JobExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\JobBoard\Api\Data\JobExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
