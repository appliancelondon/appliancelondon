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
use Magento\Framework\Exception\LocalizedException;

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

        if (!$this->getRequest()->isPost()) {
            $this->logger->warning('Invalid request method');
            return $resultJson->setData([
                'success' => false,
                'message' => __('Invalid request.')
            ]);
        }

        try {
            $postData = $this->getRequest()->getPostValue();
            $this->logger->info('Received booking data: ' . json_encode($postData));

            $this->validateRequiredFields($postData);

            $this->logger->info('Attempting to save booking');
            $bookingId = $this->booking->saveBooking($postData);

            if (!$bookingId) {
                $this->logger->error('Failed to save booking');
                throw new LocalizedException(__('There was a problem saving your booking. Please try again.'));
            }

            $booking = $this->booking->load($bookingId);
            $this->viewModel->setBooking($booking);
            
            $this->logger->info('Booking saved successfully. ID: ' . $bookingId);
            $this->logger->info('Booking data: ' . json_encode($booking->getData()));
            
            $confirmationHtml = $this->generateConfirmationHtml($booking);
            $emailSent = $this->sendConfirmationEmail($booking);

            $this->logger->info('Booking process completed successfully');
            return $resultJson->setData([
                'success' => true,
                'message' => $emailSent 
                    ? __('Your booking has been confirmed. A confirmation email has been sent to your email address.')
                    : __('Your booking has been confirmed, but there was an issue sending the confirmation email. Our team will contact you shortly.'),
                'confirmationHtml' => $confirmationHtml
            ]);

        } catch (LocalizedException $e) {
            $this->logger->error('LocalizedException: ' . $e->getMessage());
            return $resultJson->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            $this->logger->critical('Unexpected error: ' . $e->getMessage(), ['exception' => $e]);
            return $resultJson->setData([
                'success' => false,
                'message' => __('An unexpected error occurred. Please try again later.')
            ]);
        }
    }

    private function validateRequiredFields($postData)
    {
        $requiredFields = ['service', 'postcode', 'applianceType', 'applianceSubtype', 'applianceMake', 'visitDate', 'visitTime', 'faultDescription'];
        
        if ($postData['landlordAgent'] === 'yes') {
            $requiredFields = array_merge($requiredFields, [
                'tenant_title', 'tenant_firstname', 'tenant_lastname', 'tenant_email', 'tenant_phone', 'tenant_postcode', 'tenant_address1',
                'landlord_title', 'landlord_firstname', 'landlord_lastname', 'landlord_email', 'landlord_phone', 'landlord_postcode', 'landlord_address1'
            ]);
        } else {
            $requiredFields = array_merge($requiredFields, [
                'title', 'firstname', 'lastname', 'email', 'phone', 'postcode', 'address1'
            ]);
        }

        foreach ($requiredFields as $field) {
            if (empty($postData[$field])) {
                $this->logger->warning('Missing required field: ' . $field);
                throw new LocalizedException(__('Please fill in all required fields.'));
            }
        }
    }

    private function generateConfirmationHtml($booking)
    {
        $resultPage = $this->resultPageFactory->create();
        $block = $resultPage->getLayout()->createBlock(
            \Magento\Framework\View\Element\Template::class,
            'booking.confirmation',
            ['data' => [
                'template' => 'Appliancentre_BookingForm::booking/confirmation.phtml',
                'view_model' => $this->viewModel
            ]]
        );
        return $block->toHtml();
    }

    private function sendConfirmationEmail($booking)
    {
        try {
            $this->logger->info('Attempting to send confirmation email');
            $this->emailHelper->sendEmail($booking);
            $this->logger->info('Confirmation email sent successfully');
            return true;
        } catch (LocalizedException $e) {
            $this->logger->error('Failed to send confirmation email: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}