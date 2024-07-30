<?php
namespace Appliancentre\BookingForm\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Appliancentre\BookingForm\Model\Booking;

class BookingConfirmation implements ArgumentInterface
{
    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function getBooking()
    {
        return $this->booking;
    }

    public function setBooking(Booking $booking)
    {
        $this->booking = $booking;
        return $this;
    }
}