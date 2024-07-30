<?php
namespace Appliancentre\BookingForm\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Booking extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('appliancentre_bookingform', 'id');
    }
}