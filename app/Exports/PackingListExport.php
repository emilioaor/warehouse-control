<?php

namespace App\Exports;

use App\Order;
use App\PackingList;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PackingListExport implements FromCollection, WithHeadings
{

    protected $packingList;

    /**
     * PackingListExport constructor.
     * @param PackingList $packingList
     */
    public function __construct(PackingList $packingList)
    {
        $this->packingList = $packingList;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        $orders = Order::query()
            ->with(['courier', 'customer', 'orderDetails'])
            ->where('packing_list_id', $this->packingList->id)
            ->get()
        ;

        $boxes = 0;
        $weight = 0;

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {

                $data[] = [
                    'courier' => $order->courier->name,
                    'way' => __('way.' . $order->way),
                    'salesOrder' => $order->invoice_number,
                    'customer' => $order->customer->description,
                    'boxes' => $detail->qty,
                    'size' => $detail->size,
                    'weight' => $detail->weight
                ];

                $boxes += $detail->qty;
                $weight += $detail->weight * $detail->qty;
            }
        }

        $data[] = ['', '', '', '', $boxes, '', $weight];

        return new Collection($data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('validation.attributes.courier'),
            __('validation.attributes.way'),
            __('validation.attributes.salesOrder'),
            __('validation.attributes.customer'),
            __('validation.attributes.boxes'),
            __('validation.attributes.size'),
            __('validation.attributes.weight')
        ];
    }
}
