<?php
namespace Appliancentre\BookingForm\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('appliancentre_bookingform')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Booking ID'
            )->addColumn(
                'service',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Service Type'
            )->addColumn(
                'postcode',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false],
                'Postcode'
            )->addColumn(
                'appliance_type',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Appliance Type'
            )->addColumn(
                'appliance_subtype',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Appliance Subtype'
            )->addColumn(
                'appliance_make',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Appliance Make'
            )->addColumn(
                'quote_amount',
                Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false],
                'Quote Amount'
            )->addColumn(
                'customer_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Name'
            )->addColumn(
                'customer_email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Email'
            )->addColumn(
                'customer_phone',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false],
                'Customer Phone'
            )->addColumn(
                'visit_date',
                Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Visit Date'
            )->addColumn(
                'visit_time',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false],
                'Visit Time'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->setComment('Booking Form Table');

            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}