<?php
namespace Appliancentre\BookingForm\Api\Data;

interface BookingInterface
{
    const ID = 'id';
    const REFERENCE_NUMBER = 'reference_number';
    const SERVICE = 'service';
    const POSTCODE = 'postcode';
    const APPLIANCES = 'appliances';
    const VISIT_DATE = 'visit_date';
    const VISIT_TIME = 'visit_time';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_PHONE = 'customer_phone';
    const CUSTOMER_ADDRESS = 'customer_address';
    const FAULT_DESCRIPTION = 'fault_description';
    const MODEL_NUMBER = 'model_number';
    const ADDITIONAL_INFO = 'additional_info';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getReferenceNumber();

    /**
     * @param string $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber);

    /**
     * @return string|null
     */
    public function getService();

    /**
     * @param string $service
     * @return $this
     */
    public function setService($service);

    /**
     * @return string|null
     */
    public function getPostcode();

    /**
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode);

    /**
     * @return string|null
     */
    public function getAppliances();

    /**
     * @param string $appliances
     * @return $this
     */
    public function setAppliances($appliances);

    /**
     * @return string|null
     */
    public function getVisitDate();

    /**
     * @param string $visitDate
     * @return $this
     */
    public function setVisitDate($visitDate);

    /**
     * @return string|null
     */
    public function getVisitTime();

    /**
     * @param string $visitTime
     * @return $this
     */
    public function setVisitTime($visitTime);

    /**
     * @return string|null
     */
    public function getCustomerName();

    /**
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * @return string|null
     */
    public function getCustomerPhone();

    /**
     * @param string $customerPhone
     * @return $this
     */
    public function setCustomerPhone($customerPhone);

    /**
     * @return string|null
     */
    public function getCustomerAddress();

    /**
     * @param string $customerAddress
     * @return $this
     */
    public function setCustomerAddress($customerAddress);

    /**
     * @return string|null
     */
    public function getFaultDescription();

    /**
     * @param string $faultDescription
     * @return $this
     */
    public function setFaultDescription($faultDescription);

    /**
     * @return string|null
     */
    public function getModelNumber();

    /**
     * @param string $modelNumber
     * @return $this
     */
    public function setModelNumber($modelNumber);

    /**
     * @return string|null
     */
    public function getAdditionalInfo();

    /**
     * @param string $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo);

    /**
     * @return string|null
     */
    public function getStatus();

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}