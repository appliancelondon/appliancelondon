<?php
namespace Appliancentre\BookingForm\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Psr\Log\LoggerInterface;

class Email extends AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
    }

    public function sendEmail($booking)
    {
        try {
            $customerEmail = $booking->getCustomerEmail();
            if (empty($customerEmail)) {
                throw new \Exception('Customer email is empty in the booking data');
            }

            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('Appliance Centre'),
                'email' => $this->escaper->escapeHtml('info@appliancecentrelondon.co.uk'),
            ];

            // Prepare the booking data for the email template
            $emailVars = [
                'booking' => new \Magento\Framework\DataObject([
                    'getBookingName' => $booking->getFirstname() . ' ' . $booking->getLastname(),
                    'getReferenceNumber' => $booking->getReferenceNumber(),
                    'getService' => $booking->getService(),
                    'getAppliances' => json_decode($booking->getAppliances(), true),
                    'getVisitDate' => $booking->getVisitDate(),
                    'getVisitTime' => $booking->getVisitTime(),
                    'getLandlordAgent' => $booking->getLandlordAgent(),
                    'getTenantTitle' => $booking->getTenantTitle(),
                    'getTenantFirstname' => $booking->getTenantFirstname(),
                    'getTenantLastname' => $booking->getTenantLastname(),
                    'getTenantAddress1' => $booking->getTenantAddress1(),
                    'getTenantAddress2' => $booking->getTenantAddress2(),
                    'getTenantCity' => $booking->getTenantCity(),
                    'getTenantPhone' => $booking->getTenantPhone(),
                    'getTenantEmail' => $booking->getTenantEmail(),
                    'getTitle' => $booking->getTitle(),
                    'getFirstname' => $booking->getFirstname(),
                    'getLastname' => $booking->getLastname(),
                    'getLandlordCompany' => $booking->getLandlordCompany(),
                    'getAddress1' => $booking->getAddress1(),
                    'getAddress2' => $booking->getAddress2(),
                    'getCity' => $booking->getCity(),
                    'getPhone' => $booking->getPhone(),
                    'getEmail' => $booking->getEmail(),
                ])
            ];

            $transport = $this->transportBuilder
                ->setTemplateId(1) // Use the template ID instead of identifier
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars($emailVars)
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $this->logger->info('Confirmation email sent successfully to: ' . $customerEmail);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send confirmation email: ' . $e->getMessage());
        }
    }
}
