<?php
namespace Appliancentre\BookingForm\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class BookingCounter extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('booking_counter', 'id');
    }
}