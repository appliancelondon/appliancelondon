<?php
namespace Appliancentre\BookingForm\Model\ResourceModel\Booking;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Appliancentre\BookingForm\Model\Booking::class,
            \Appliancentre\BookingForm\Model\ResourceModel\Booking::class
        );
    }
}