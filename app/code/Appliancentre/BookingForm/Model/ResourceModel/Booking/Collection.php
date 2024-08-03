<?php
namespace Appliancentre\BookingForm\Model\ResourceModel\Booking;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'appliancentre_bookingform_booking_collection';
    protected $_eventObject = 'booking_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Appliancentre\BookingForm\Model\Booking::class,
            \Appliancentre\BookingForm\Model\ResourceModel\Booking::class
        );
    }
}