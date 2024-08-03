<?php
namespace Appliancentre\BookingForm\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;

class Booking extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'appliancentre_bookingform_booking';

    protected $_cacheTag = 'appliancentre_bookingform_booking';
    protected $_eventPrefix = 'appliancentre_bookingform_booking';

    protected function _construct()
    {
        $this->_init('Appliancentre\BookingForm\Model\ResourceModel\Booking');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function saveBooking($data)
    {
        $referenceNumber = $this->getNextReferenceNumber();
        $data['reference_number'] = $referenceNumber;
        
        $this->_logger->info('Saving booking data: ' . json_encode($data));
        
        $isLandlord = isset($data['landlordAgent']) && $data['landlordAgent'] === 'yes';
        
        if ($isLandlord) {
            $data['customer_name'] = $data['tenant_title'] . ' ' . $data['tenant_firstname'] . ' ' . $data['tenant_lastname'];
            $data['customer_email'] = $data['tenant_email'];
            $data['customer_phone'] = $data['tenant_phone'];
            $data['customer_address'] = $this->formatAddress($data, 'tenant_');
            
            $data['landlord_name'] = $data['landlord_title'] . ' ' . $data['landlord_firstname'] . ' ' . $data['landlord_lastname'];
            $data['landlord_email'] = $data['landlord_email'];
            $data['landlord_phone'] = $data['landlord_phone'];
            $data['landlord_address'] = $this->formatAddress($data, 'landlord_');
        } else {
            $data['customer_name'] = $data['title'] . ' ' . $data['firstname'] . ' ' . $data['lastname'];
            $data['customer_email'] = $data['email'];
            $data['customer_phone'] = $data['phone'];
            $data['customer_address'] = $this->formatAddress($data);
        }
        
        // Ensure these fields are saved
        $data['appliance_type'] = $data['applianceType'];
        $data['appliance_subtype'] = $data['applianceSubtype'];
        $data['appliance_make'] = $data['applianceMake'];
        $data['visit_date'] = $data['visitDate'];
        $data['visit_time'] = $data['visitTime'];
        $data['fault_description'] = $data['faultDescription'];
        $data['model_number'] = $data['modelSerial'] ?? '';
        $data['additional_info'] = $data['additionalInfo'] ?? '';
        
        $this->setData($data);
        $this->save();
        
        $this->_logger->info('Saved booking data: ' . json_encode($this->getData()));

        return $this->getId();
    }

    protected function formatAddress($data, $prefix = '')
    {
        $addressParts = [
            $data[$prefix . 'address1'],
            $data[$prefix . 'address2'] ?? '',
            $data[$prefix . 'city'] ?? '',
            $data[$prefix . 'postcode']
        ];
        return implode(', ', array_filter($addressParts));
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
            throw new LocalizedException(__('Unable to generate reference number. Please try again.'));
        }
    }

    public function getReferenceNumber()
    {
        return $this->getData('reference_number');
    }

    public function getApplianceMake()
    {
        return $this->getData('appliance_make');
    }

    public function getApplianceType()
    {
        return $this->getData('appliance_type');
    }

    public function getApplianceSubtype()
    {
        return $this->getData('appliance_subtype');
    }

    public function getModelNumber()
    {
        return $this->getData('model_number');
    }

    public function getVisitDate()
    {
        return $this->getData('visit_date');
    }

    public function getVisitTime()
    {
        return $this->getData('visit_time');
    }
}