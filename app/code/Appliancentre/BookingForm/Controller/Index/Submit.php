<?php
namespace Appliancentre\BookingForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Appliancentre\BookingForm\Model\Booking;
use Appliancentre\BookingForm\ViewModel\BookingConfirmation;
use Appliancentre\BookingForm\Helper\Email;
use Psr\Log\LoggerInterface;

class Submit extends Action
{
    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $booking;
    protected $viewModel;
    protected $emailHelper;
    protected $logger;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        Booking $booking,
        BookingConfirmation $viewModel,
        Email $emailHelper,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->booking = $booking;
        $this->viewModel = $viewModel;
        $this->emailHelper = $emailHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
{
    $resultJson = $this->resultJsonFactory->create();

    if ($this->getRequest()->isPost()) {
        try {
            $postData = $this->getRequest()->getPostValue();
            
            $this->logger->info('Received booking data: ' . json_encode($postData));

            // Validate required fields
            $requiredFields = ['service', 'postcode', 'appliances', 'visitDate', 'visitTime', 'email'];
            foreach ($requiredFields as $field) {
                if (empty($postData[$field])) {
                    $this->logger->warning('Missing required field: ' . $field);
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please fill in all required fields.'));
                }
            }

            // Validate appliances
            if (!is_array($postData['appliances']) || empty($postData['appliances'])) {
                $this->logger->warning('Invalid appliances data');
                throw new \Magento\Framework\Exception\LocalizedException(__('Please provide at least one appliance.'));
            }

            foreach ($postData['appliances'] as $appliance) {
                if (empty($appliance['applianceType']) || empty($appliance['applianceSubtype']) || empty($appliance['applianceMake'])) {
                    $this->logger->warning('Invalid appliance data: ' . json_encode($appliance));
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please provide complete information for all appliances.'));
                }
            }

            // Validate postcode
            if (!$this->booking->isValidPostcode($postData['postcode'])) {
                $this->logger->warning('Invalid postcode: ' . $postData['postcode']);
                return $resultJson->setData([
                    'success' => false,
                    'message' => __('Sorry, we do not cover this area.')
                ]);
            }

            // Save booking
            $this->logger->info('Saving booking');
            $bookingId = $this->booking->saveBooking($postData);

            if ($bookingId) {
                $this->logger->info('Booking saved successfully. ID: ' . $bookingId);
                $booking = $this->booking->load($bookingId);
                $this->viewModel->setBooking($booking);
                
                // Send confirmation email
                $customerEmail = $booking->getCustomerEmail();
                if ($customerEmail) {
                    $this->logger->info('Sending confirmation email to: ' . $customerEmail);
                    $this->emailHelper->sendEmail($booking);
                } else {
                    $this->logger->warning('Customer email is missing in booking data');
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
                $this->logger->error('Failed to save booking');
                return $resultJson->setData([
                    'success' => false,
                    'message' => __('There was a problem saving your booking. Please try again.')
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error in booking submission: ' . $e->getMessage(), ['exception' => $e]);
            return $resultJson->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    $this->logger->warning('Invalid request method');
    return $resultJson->setData([
        'success' => false,
        'message' => __('Invalid request.')
    ]);
}
}
