<?php
namespace Appliancentre\CustomerBookings\Block\Booking;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Appliancentre\BookingForm\Model\ResourceModel\Booking\CollectionFactory;
use Magento\Customer\Model\Session;

class Index extends Template
{
    protected $bookingCollectionFactory;
    protected $customerSession;

    public function __construct(
        Context $context,
        CollectionFactory $bookingCollectionFactory,
        Session $customerSession,
        array $data = []
    ) {
        $this->bookingCollectionFactory = $bookingCollectionFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getCustomerBookings()
    {
        $customerId = $this->customerSession->getCustomerId();
        $collection = $this->bookingCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        return $collection;
    }
}