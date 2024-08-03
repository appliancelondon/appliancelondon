<?php
namespace Appliancentre\BookingForm\Plugin;

use Magento\Framework\Data\Tree\NodeFactory;

class AddBookingFormToTopmenu
{
    protected $nodeFactory;

    public function __construct(
        NodeFactory $nodeFactory
    ) {
        $this->nodeFactory = $nodeFactory;
    }

    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        $node = $this->nodeFactory->create(
            [
                'data' => [
                    'name' => __('Book a Repair'),
                    'id' => 'booking-form',
                    'url' => '/bookingform',
                    'has_active' => false,
                    'is_active' => false
                ],
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        $subject->getMenu()->addChild($node);
    }
}