<?php
namespace Appliancentre\BookingForm\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Booking extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'appliancentre_bookingform_booking';

    protected $_cacheTag = 'appliancentre_bookingform_booking';
    protected $_eventPrefix = 'appliancentre_bookingform_booking';

    protected $customerFactory;
    protected $accountManagement;
    protected $encryptor;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerFactory $customerFactory,
        AccountManagementInterface $accountManagement,
        EncryptorInterface $encryptor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
        $this->encryptor = $encryptor;
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

    public function getSubmissionCount()
    {
        $collection = $this->getCollection();
        return $collection->getSize();
    }

    public function calculateQuoteTotal($subTotal, $classFieldRadio)
    {
        // Implement your quote calculation logic here
        return $subTotal + $classFieldRadio;
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
                
                // You might want to send an email to the customer with their account details here
            }
            return $customer->getId();
        } catch (\Exception $e) {
            $this->_logger->critical('Error handling customer account: ' . $e->getMessage());
        }
    }

    protected function generatePassword()
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12);
    }
}
