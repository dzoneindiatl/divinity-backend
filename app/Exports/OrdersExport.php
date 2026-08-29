<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    protected $selects;
    protected $rowCount;
    protected $columnCount;

    public function __construct()
    {
        // Define columns and their respective dropdown options
        $genders = ['Male', 'Female', 'Other'];
        $this->selects = [
            ['columns_name' => 'D', 'options' => $genders],
        ];
        $this->rowCount = Order::count() + 1; // Adding 1 for the header row
        $this->columnCount = 19; // Number of columns in the Excel sheet (now includes order item columns)
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Eager load the order items with the order (also load necessary relationships like product, etc.)
        return Order::with('items.product')->get();
    }

    public function headings(): array
    {
        return [
            'Sr No.',
            'Order ID',
            'Customer Name',
            'Mobile No.',
            'Email Id',
            'Address Line 1',
            'Address Line 2',
            'City',
            'State',
            'Payment Mode',
            'MRP',
            'Selling Price',
            'Taxable Amount',
            'Tax %',
            'Tax Amount',
            'Order Status',
            'Courier Name',
            'Tracking ID',
            'Tracking Link'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $row_count = $this->rowCount;
                $column_count = $this->columnCount;

                // Set background color for the header row (row 1)
                $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_count) . '1';
                $event->sheet->getStyle($headerRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFFF00',  // Yellow color (change as needed)
                        ],
                    ],
                    'font' => [
                        'bold' => true,  // Make header text bold
                        'color' => ['argb' => '000000'],  // Black text color (optional)
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Set column width to auto size based on content
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }


    public function map($order): array
    {
        // Get billing and shipping address data
        if ($order->billing_address) {
            $b = json_decode($order->billing_address, true);
            $billingAddress = "{$b['billing_address']} {$b['billing_city']} {$b['billing_state']} {$b['billing_country']} - {$b['billing_pincode']}";
        } else {
            $billingAddress = null;
        }

        if ($order->shipping_address) {
            $s = json_decode($order->shipping_address);
            $shippingAddress = "{$s->shipping_address} {$s->shipping_city} {$s->shipping_state} {$s->shipping_country} - {$s->shipping_pincode}";
        } else {
            $shippingAddress = null;
        }

        // Initialize rows array
        $rows = [];

        // Serial number for each order
        static $srno = 1; // Static to persist the serial number across orders
        // Loop through each order item and create a new row for each
        foreach ($order->items as $index => $orderItem) {
            $mrp = $orderItem->product->selling_price ?? null;
            $productName = $orderItem->product->name ?? null;
            /*$city = "";
            $state = "";
            $postal_code = "";
            foreach ($order->user->address as $addr) {
                $city = $addr->city->name ?? null;
                $state = $addr->state->name ?? null;
                $postal_code = $addr->postal_code ?? null;
                if($addr->is_primary == 1){
                    $city = $addr->city->name ?? null;
                    $state = $addr->state->name ?? null;
                    $postal_code = $addr->postal_code ?? null;
                }
            }*/
            $city = $state = $postal_code = null;
            if (!empty($order->user->address) && is_iterable($order->user->address)) {
                foreach ($order->user->address as $addr) {
                    if (isset($addr->is_primary) && $addr->is_primary == 1) {
                        $city = $addr->city->name ?? null;
                        $state = $addr->state->name ?? null;
                        $postal_code = $addr->postal_code ?? null;
                        break; // Primary address found, no need to continue
                    }
                }
                // Fallback if no primary address is found
                if ($city === null && $state === null && $postal_code === null) {
                    $firstAddr = $order->user->address[0] ?? null;
                    if ($firstAddr) {
                        $city = $firstAddr->city->name ?? null;
                        $state = $firstAddr->state->name ?? null;
                        $postal_code = $firstAddr->postal_code ?? null;
                    }
                }
            }
            $status = "";
            if ($order->status == 1) {
                $status = "Pending";
            } elseif ($order->status == 2) {
                $status = "Accepted";
            } elseif ($order->status == 3) {
                $status = "Shipped";
            } elseif ($order->status == 4) {
                $status = "In Transit";
            } elseif ($order->status == 5) {
                $status = "Out for Delivery";
            } elseif ($order->status == 6) {
                $status = "Delivered";
            } elseif ($order->status == 7) {
                $status = "Return Requested";
            } elseif ($order->status == 8) {
                $status = "Return Accepted";
            } elseif ($order->status == 9) {
                $status = "Refund Pending";
            } elseif ($order->status == 10) {
                $status = "Refunded";
            } elseif ($order->status == 11) {
                $status = "Cancelled";
            }

            $tax_price = 0;
            $tax_rates = [];
            foreach ($order->orderItem as $item) {
                $tax_price += $item->tax_price ?? 0;
                if (!empty($item->tax_val)) {
                    $tax_rates[] = $item->tax_val;
                }
            }
            $tax_rate = implode(', ', array_unique($tax_rates));
            // For the first item, add order details and serial number
            if ($index === 0) {
                // Add the serial number and order details for the first item
                $rows[] = [
                    $srno++,                      // Serial number (incremented for the first item)
                    $order->order_number ?? null,
                    $order->user->name ?? null,
                    $order->user->phone_number ?? null,
                    $order->user->email ?? null,
                    $billingAddress,
                    $shippingAddress,
                    $city,
                    $state,
                    $order->payment_method ?? null,
                    $mrp ? 'Rs. ' . number_format($mrp, 2) : null,
                    $orderItem->total ? 'Rs. ' . number_format($orderItem->total, 2) : null,
                    $orderItem->sub_total ? 'Rs. ' . number_format($orderItem->sub_total, 2) : null,
                    $tax_rate,
                    $tax_price,
                    $status,
                    $productName,
                    $order->awb_number ?? null,
                    $order->tracking_url ?? null,
                ];
            } else {
                $rows[] = [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
            }
        }

        return $rows;
    }
}