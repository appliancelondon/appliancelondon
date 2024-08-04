<?php
namespace Appliancentre\BookingForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Appliancentre\BookingForm\Model\Booking;
use Appliancentre\BookingForm\ViewModel\BookingConfirmation;
use Appliancentre\BookingForm\Helper\Email;

class Submit extends Action
{
    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $booking;
    protected $viewModel;
    protected $emailHelper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        Booking $booking,
        BookingConfirmation $viewModel,
        Email $emailHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->booking = $booking;
        $this->viewModel = $viewModel;
        $this->emailHelper = $emailHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPostValue();
                
                // Log received data
                $this->_eventManager->dispatch('logging_event', ['message' => 'Received booking data: ' . json_encode($postData)]);

                // Validate required fields
                $requiredFields = ['service', 'postcode', 'appliances', 'visitDate', 'visitTime'];
                foreach ($requiredFields as $field) {
                    if (empty($postData[$field])) {
                        $this->_eventManager->dispatch('logging_event', ['message' => 'Missing required field: ' . $field]);
                        throw new \Magento\Framework\Exception\LocalizedException(__('Please fill in all required fields.'));
                    }
                }

                // Validate appliances
                if (!is_array($postData['appliances']) || empty($postData['appliances'])) {
                    $this->_eventManager->dispatch('logging_event', ['message' => 'Invalid appliances data']);
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please provide at least one appliance.'));
                }

                foreach ($postData['appliances'] as $appliance) {
                    if (empty($appliance['applianceType']) || empty($appliance['applianceSubtype']) || empty($appliance['applianceMake'])) {
                        $this->_eventManager->dispatch('logging_event', ['message' => 'Invalid appliance data: ' . json_encode($appliance)]);
                        throw new \Magento\Framework\Exception\LocalizedException(__('Please provide complete information for all appliances.'));
                    }
                }

                // Validate postcode
                if (!$this->booking->isValidPostcode($postData['postcode'])) {
                    return $resultJson->setData([
                        'success' => false,
                        'message' => __('Sorry, we do not cover this area.')
                    ]);
                }

                // Save booking
                $bookingId = $this->booking->saveBooking($postData);


                if ($bookingId) {
    $booking = $this->booking->load($bookingId);
    $this->viewModel->setBooking($booking);
    
    // Send confirmation email only if email is present
    if ($booking->getCustomerEmail()) {
        $this->emailHelper->sendEmail($booking);
    } else {
        $this->_eventManager->dispatch('logging_event', ['message' => 'Customer email is missing in booking data']);
    }

                    // Render confirmation page
                    $resultPage = $this->resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->set(__('Booking Confirmation'));
                    $block = $resultPage->getLayout()->getBlock('booking.confirmation');
                    $block->setViewModel($this->viewModel);

                    // Get the HTML content of the confirmation page
                    $confirmationHtml = $block->toHtml();

                    return $resultJson->setData([
                        'success' => true,
                        'message' => __('Your booking has been confirmed.'),
                        'confirmationHtml' => $confirmationHtml
                    ]);
                } else {
                    return $resultJson->setData([
                        'success' => false,
                        'message' => __('There was a problem saving your booking. Please try again.')
                    ]);
                }
            } catch (\Exception $e) {
                $this->_eventManager->dispatch('logging_event', ['message' => 'Error in booking submission: ' . $e->getMessage()]);
                return $resultJson->setData([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }

        return $resultJson->setData([
            'success' => false,
            'message' => __('Invalid request.')
        ]);
    }
}
