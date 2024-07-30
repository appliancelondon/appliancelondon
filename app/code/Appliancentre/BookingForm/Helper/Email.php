<?php
namespace Appliancentre\BookingForm\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Email extends AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->scopeConfig = $scopeConfig;
    }

    public function sendEmail($booking)
    {
        try {
            $this->logger->info('Preparing to send confirmation email');
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml($this->scopeConfig->getValue('trans_email/ident_general/name', ScopeInterface::SCOPE_STORE)),
                'email' => $this->escaper->escapeHtml($this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE)),
            ];
            $this->logger->info('Sender details: ' . json_encode($sender));

            $customerEmail = $booking->getEmail();
            $this->logger->info('Customer email: ' . $customerEmail);

            if (empty($customerEmail)) {
                throw new \Exception('Customer email is empty');
            }

            $transport = $this->transportBuilder
                ->setTemplateIdentifier(1)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['booking' => $booking])
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();

            $this->logger->info('Email transport prepared');
            $transport->sendMessage();
            $this->logger->info('Email sent successfully');
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->error('Error sending email: ' . $e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw $e; // Re-throw the exception to be caught in the controller
        }
    }
}