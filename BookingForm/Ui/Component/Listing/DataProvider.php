<?php
namespace Appliancentre\BookingForm\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;
use Psr\Log\LoggerInterface;

class DataProvider extends UiDataProvider
{
    protected $logger;
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        LoggerInterface $logger,
        \Appliancentre\BookingForm\Model\ResourceModel\Booking\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->logger = $logger;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        $data = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items['items'] ?? $items),
        ];

        $this->logger->info('DataProvider getData: ' . json_encode($data));
        return $data;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();

        if (in_array($field, ['id', 'reference_number', 'email'])) {
            $condition = [$filter->getConditionType() => $filter->getValue()];
            $this->collection->addFieldToFilter($field, $condition);
        }

        return $this;
    }
}