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
                $requiredFields = ['service', 'postcode', 'applianceType', 'applianceSubtype', 'applianceMake', 'visitDate', 'visitTime'];
                foreach ($requiredFields as $field) {
                    if (empty($postData[$field])) {
                        $this->logger->warning('Missing required field: ' . $field);
                        return $resultJson->setData([
                            'success' => false,
                            'message' => __('Please fill in all required fields.')
                        ]);
                    }
                }

                // Validate postcode
                if (!$this->booking->isValidPostcode($postData['postcode'])) {
                    $this->logger->info('Invalid postcode: ' . $postData['postcode']);
                    return $resultJson->setData([
                        'success' => false,
                        'message' => __('Sorry, we do not cover this area.')
                    ]);
                }

                $this->logger->info('Attempting to save booking');
                $bookingId = $this->booking->saveBooking($postData);

                if ($bookingId) {
                    $this->logger->info('Booking saved successfully. ID: ' . $bookingId);
                    $booking = $this->booking->load($bookingId);
                    $this->viewModel->setBooking($booking);
                    
                    // Send confirmation email
                    $emailSent = false;
                    try {
                        $this->logger->info('Attempting to send confirmation email');
                        $this->emailHelper->sendEmail($booking);
                        $this->logger->info('Confirmation email sent successfully');
                        $emailSent = true;
                    } catch (\Exception $e) {
                        $this->logger->error('Failed to send confirmation email: ' . $e->getMessage(), [
                            'exception' => $e,
                            'trace' => $e->getTraceAsString()
                        ]);
                        // Continue with the process even if email fails
                    }

                    // Render confirmation page
                    $this->logger->info('Rendering confirmation page');
                    $resultPage = $this->resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->set(__('Booking Confirmation'));
                    $block = $resultPage->getLayout()->getBlock('booking.confirmation');
                    
                    if (!$block) {
                        throw new \Exception('Confirmation block not found');
                    }
                    
                    $block->setViewModel($this->viewModel);

                    // Get the HTML content of the confirmation page
                    $confirmationHtml = $block->toHtml();

                    $this->logger->info('Booking process completed successfully');
                    return $resultJson->setData([
                        'success' => true,
                        'message' => $emailSent 
                            ? __('Your booking has been confirmed.')
                            : __('Your booking has been confirmed, but there was an issue sending the confirmation email. Our team will contact you shortly.'),
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
                $this->logger->critical('Booking submission error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return $resultJson->setData([
                    'success' => false,
                    'message' => __('An unexpected error occurred. Please try again later.'),
                    'error_details' => __('Error: %1', $e->getMessage()) // Remove this line in production
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