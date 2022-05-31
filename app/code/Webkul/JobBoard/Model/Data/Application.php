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

use Webkul\JobBoard\Api\Data\ApplicationInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Application extends AbstractExtensibleObject implements ApplicationInterface
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
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get job
     * @return string|null
     */
    public function getJob()
    {
        return $this->_get(self::JOB);
    }

    /**
     * Set job
     * @param string $job
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setJob($job)
    {
        return $this->setData(self::JOB, $job);
    }

    /**
     * Get firstname
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->_get(self::FIRSTNAME);
    }

    /**
     * Set firstname
     * @param string $firstname
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setFirstname($firstname)
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * Get lastname
     * @return string|null
     */
    public function getLastname()
    {
        return $this->_get(self::LASTNAME);
    }

    /**
     * Set lastname
     * @param string $lastname
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setLastname($lastname)
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * Get email
     * @return string|null
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

/* Start By Dharms */

 /**
     * Get telephone
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->_get(self::Telephone);
    }

    /**
     * Set telephone
     * @param string $telephone
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::Telephone, $telephone);
    }

/** End By Dharms   */


    /**
     * Get address
     * @return string|null
     */
    public function getAddress()
    {
        return $this->_get(self::ADDRESS);
    }

    /**
     * Set address
     * @param string $address
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * Get qualification
     * @return string|null
     */
    public function getQualification()
    {
        return $this->_get(self::QUALIFICATION);
    }

    /**
     * Set qualification
     * @param string $qualification
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setQualification($qualification)
    {
        return $this->setData(self::QUALIFICATION, $qualification);
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
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setExperience($experience)
    {
        return $this->setData(self::EXPERIENCE, $experience);
    }

    /**
     * Get position
     * @return string|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Set position
     * @param string $position
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Get company
     * @return string|null
     */
    public function getCompany()
    {
        return $this->_get(self::COMPANY);
    }

    /**
     * Set company
     * @param string $company
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setCompany($company)
    {
        return $this->setData(self::COMPANY, $company);
    }

    /**
     * Get resume
     * @return string|null
     */
    public function getResume()
    {
        return $this->_get(self::RESUME);
    }

    /**
     * Set resume
     * @param string $resume
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setResume($resume)
    {
        return $this->setData(self::RESUME, $resume);
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
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
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
     * @return \Webkul\JobBoard\Api\Data\ApplicationInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\JobBoard\Api\Data\ApplicationExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Webkul\JobBoard\Api\Data\ApplicationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\JobBoard\Api\Data\ApplicationExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
