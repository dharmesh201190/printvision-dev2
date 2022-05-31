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

interface ApplicationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @var String
     */
    const ENTITY_ID = 'entity_id';

    /**
     * @var String
     */
    const JOB = 'job';

    /**
     * @var String
     */
    const FIRSTNAME = 'firstname';

    /**
     * @var String
     */
    const LASTNAME = 'lastname';

    /**
     * @var String
     */
    const EMAIL = 'email';

    /**
     * @var String
     */
    const ADDRESS = 'address';

    /**
     * @var String
     */
    const QUALIFICATION = 'qualification';

    /**
     * @var String
     */
    const EXPERIENCE = 'experience';
    
    /**
     * @var String
     */
    const POSITION = 'position';

    /**
     * @var String
     */
    const COMPANY = 'company';

    /**
     * @var String
     */
    const RESUME = 'resume';
    
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
     * @param string $applicationId
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setId($id);

    /**
     * Get job
     * @return string|null
     */
    public function getJob();

    /**
     * Set job
     * @param string $job
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setJob($job);

    /**
     * Get firstname
     * @return string|null
     */
    public function getFirstname();

    /**
     * Set firstname
     * @param string $firstname
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setFirstname($firstname);

    /**
     * Get lastname
     * @return string|null
     */
    public function getLastname();

    /**
     * Set lastname
     * @param string $lastname
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setLastname($lastname);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setEmail($email);

    /**
     * Get address
     * @return string|null
     */
    public function getAddress();

    /**
     * Set address
     * @param string $address
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setAddress($address);

    /**
     * Get qualification
     * @return string|null
     */
    public function getQualification();

    /**
     * Set qualification
     * @param string $qualification
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setQualification($qualification);

    /**
     * Get experience
     * @return string|null
     */
    public function getExperience();

    /**
     * Set experience
     * @param string $experience
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setExperience($experience);

    /**
     * Get position
     * @return string|null
     */
    public function getPosition();

    /**
     * Set position
     * @param string $position
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setPosition($position);

    /**
     * Get company
     * @return string|null
     */
    public function getCompany();

    /**
     * Set company
     * @param string $company
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setCompany($company);

    /**
     * Get resume
     * @return string|null
     */
    public function getResume();

    /**
     * Set resume
     * @param string $resume
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setResume($resume);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
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
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\JobBoard\Api\Data\ApplicationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\JobBoard\Api\Data\ApplicationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\JobBoard\Api\Data\ApplicationExtensionInterface $extensionAttributes
    );
}
