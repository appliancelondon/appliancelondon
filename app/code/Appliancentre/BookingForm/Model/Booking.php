<?php
namespace Appliancentre\BookingForm\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Psr\Log\LoggerInterface;
use Appliancentre\BookingForm\Api\Data\BookingInterface;

class Booking extends AbstractModel implements IdentityInterface, BookingInterface
{
    const CACHE_TAG = 'appliancentre_bookingform_booking';

    protected $_cacheTag = 'appliancentre_bookingform_booking';
    protected $_eventPrefix = 'appliancentre_bookingform_booking';

    protected $customerFactory;
    protected $accountManagement;
    protected $encryptor;
    protected $logger;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerFactory $customerFactory,
        AccountManagementInterface $accountManagement,
        EncryptorInterface $encryptor,
        LoggerInterface $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
        $this->encryptor = $encryptor;
        $this->logger = $logger;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Appliancentre\BookingForm\Model\ResourceModel\Booking');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    public function getNextReferenceNumber()
    {
        $connection = $this->getResource()->getConnection();
        $connection->beginTransaction();
        try {
            $select = $connection->select()
                ->from($this->getResource()->getTable('booking_counter'), 'counter')
                ->forUpdate(true);
            $counter = $connection->fetchOne($select);
            $counter++;
            $connection->update($this->getResource()->getTable('booking_counter'), ['counter' => $counter]);
            $connection->commit();
            return $counter;
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->critical('Error generating reference number: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function saveBooking($data)
    {
        $referenceNumber = $this->getNextReferenceNumber();
        $data['reference_number'] = $referenceNumber;
        
        // Handle multiple appliances
        if (isset($data['appliances']) && is_array($data['appliances'])) {
            $appliances = [];
            foreach ($data['appliances'] as $appliance) {
                $appliances[] = [
                    'type' => $appliance['applianceType'],
                    'subtype' => $appliance['applianceSubtype'],
                    'make' => $appliance['applianceMake']
                ];
            }
            $data['appliances'] = json_encode($appliances);
        }

        // Ensure customer email is saved
        if (!empty($data['email'])) {
            $this->setCustomerEmail($data['email']);
        }

        $this->setData($data);
        $this->save();

        // Create customer account
        $this->createCustomerAccount($data);

        return $this->getId();
    }

    public function getAppliances()
    {
        $appliances = $this->getData('appliances');
        return is_string($appliances) ? $appliances : '[]';
    }

    public function isValidPostcode($postcode)
    {
        // Implement your postcode validation logic here
        // This is a simple example, you should replace it with your actual validation
        $validPostcodes = ['N1', 'N2', 'N3', 'N4', 'N5']; // Add your valid postcodes here
        $postcode = strtoupper(substr($postcode, 0, 2)); // Get first two characters of postcode
        return in_array($postcode, $validPostcodes);
    }

    protected function createCustomerAccount($data)
    {
        try {
            // Check if customer already exists
            $customer = $this->customerFactory->create()->setWebsiteId(1)->loadByEmail($data['email']);
            if (!$customer->getId()) {
                // Create new customer if not exists
                $customer->setEmail($data['email'])
                    ->setFirstname($data['firstname'])
                    ->setLastname($data['lastname'])
                    ->setPassword($this->generatePassword())
                    ->save();
                
                $this->logger->info('New customer account created: ' . $data['email']);
            } else {
                $this->logger->info('Existing customer account found: ' . $data['email']);
            }
            return $customer->getId();
        } catch (\Exception $e) {
            $this->logger->critical('Error handling customer account: ' . $e->getMessage());
        }
    }

    protected function generatePassword()
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12);
    }

    // Implement BookingInterface methods
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getReferenceNumber()
    {
        return $this->getData(self::REFERENCE_NUMBER);
    }

    public function setReferenceNumber($referenceNumber)
    {
        return $this->setData(self::REFERENCE_NUMBER, $referenceNumber);
    }

    public function getService()
    {
        return $this->getData(self::SERVICE);
    }

    public function setService($service)
    {
        return $this->setData(self::SERVICE, $service);
    }

    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    public function setAppliances($appliances)
    {
        return $this->setData(self::APPLIANCES, $appliances);
    }

    public function getVisitDate()
    {
        return $this->getData(self::VISIT_DATE);
    }

    public function setVisitDate($visitDate)
    {
        return $this->setData(self::VISIT_DATE, $visitDate);
    }

    public function getVisitTime()
    {
        return $this->getData(self::VISIT_TIME);
    }

    public function setVisitTime($visitTime)
    {
        return $this->setData(self::VISIT_TIME, $visitTime);
    }

    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    public function getCustomerPhone()
    {
        return $this->getData(self::CUSTOMER_PHONE);
    }

    public function setCustomerPhone($customerPhone)
    {
        return $this->setData(self::CUSTOMER_PHONE, $customerPhone);
    }

    public function getCustomerAddress()
    {
        return $this->getData(self::CUSTOMER_ADDRESS);
    }

    public function setCustomerAddress($customerAddress)
    {
        return $this->setData(self::CUSTOMER_ADDRESS, $customerAddress);
    }

    public function getFaultDescription()
    {
        return $this->getData(self::FAULT_DESCRIPTION);
    }

    public function setFaultDescription($faultDescription)
    {
        return $this->setData(self::FAULT_DESCRIPTION, $faultDescription);
    }

    public function getModelNumber()
    {
        return $this->getData(self::MODEL_NUMBER);
    }

    public function setModelNumber($modelNumber)
    {
        return $this->setData(self::MODEL_NUMBER, $modelNumber);
    }

    public function getAdditionalInfo()
    {
        return $this->getData(self::ADDITIONAL_INFO);
    }

    public function setAdditionalInfo($additionalInfo)
    {
        return $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
