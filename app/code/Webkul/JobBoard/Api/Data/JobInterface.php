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

interface JobInterface extends ExtensibleDataInterface
{
    /**
     * @var String
     */
    const ENTITY_ID = 'id';

    /**
     * @var String
     */
    const DESIGNATION = 'designation';

    /**
     * @var String
     */
    const CATEGORY = 'category';

    /**
     * @var String
     */
    const DESCRIPTION = 'description';

    /**
     * @var String
     */
    const ELIGIBILITY = 'eligibility';

    /**
     * @var String
     */
    const LOCATION = 'location';

    /**
     * @var String
     */
    const SKILLS = 'skills';
    
    /**
     * @var String
     */
    const SALARY_TYPE = 'salary_type';

    /**
     * @var String
     */
    const SALARY = 'salary';

    /**
     * @var String
     */
    const EXPERIENCE = 'experience';

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
    const UPDATED_AT = 'updated_at';
    
    /**
     * @var String
     */
    const CREATED_AT = 'created_at';
    
    /**
     * Get Id
     * @return string|null
     */
    public function getId();

    /**
     * Set Id
     * @param string $id
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setId($id);

    /**
     * Get designation
     * @return string|null
     */
    public function getDesignation();

    /**
     * Set designation
     * @param string $designation
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setDesignation($designation);

    /**
     * Get category
     * @return string|null
     */
    public function getCategory();

    /**
     * Set category
     * @param string $category
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setCategory($category);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setDescription($description);

    /**
     * Get eligibility
     * @return string|null
     */
    public function getEligibility();

    /**
     * Set eligibility
     * @param string $eligibility
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setEligibility($eligibility);

    /**
     * Get location
     * @return string|null
     */
    public function getLocation();

    /**
     * Set location
     * @param string $location
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setLocation($location);

    /**
     * Get skills
     * @return string|null
     */
    public function getSkills();

    /**
     * Set skills
     * @param string $skills
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setSkills($skills);

    /**
     * Get salary_type
     * @return string|null
     */
    public function getSalaryType();

    /**
     * Set salary_type
     * @param string $salaryType
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setSalaryType($salaryType);

    /**
     * Get salary
     * @return string|null
     */
    public function getSalary();

    /**
     * Set salary
     * @param string $salary
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setSalary($salary);

    /**
     * Get experience
     * @return string|null
     */
    public function getExperience();

    /**
     * Set experience
     * @param string $experience
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setExperience($experience);

    /**
     * Get sort
     * @return string|null
     */
    public function getSort();

    /**
     * Set sort
     * @param string $sort
     * @return \Webkul\JobBoard\Api\Data\JobInterface
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
     * @return \Webkul\JobBoard\Api\Data\JobInterface
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
     * @return \Webkul\JobBoard\Api\Data\JobInterface
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
     * @return \Webkul\JobBoard\Api\Data\JobInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\JobBoard\Api\Data\JobExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\JobBoard\Api\Data\JobExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\JobBoard\Api\Data\JobExtensionInterface $extensionAttributes
    );
}
