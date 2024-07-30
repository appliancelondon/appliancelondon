<?php
namespace Appliancentre\BookingForm\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;

class DataProvider extends UiDataProvider
{
    public function getData()
    {
        $data = parent::getData();
        // You can modify $data here if needed
        return $data;
    }
}