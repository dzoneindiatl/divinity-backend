<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderItemsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = OrderItem::with(['order', 'product', 'orderStatus','orderStatusHistory','cancelRequest','refundRequest']);

        if (!empty($this->filters['order_number'])) {
            $orderNumber = $this->filters['order_number'];
            $query->whereHas('order', function ($q) use ($orderNumber) {
                $q->where('orders.order_number', 'like', "%{$orderNumber}%");
            });
        }

        if (!empty($this->filters['product_name'])) {
            $productName = $this->filters['product_name'];
            $query->whereHas('product', function ($q) use ($productName) {
                $q->where('products.name', 'like', "%{$productName}%");
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('order_items.status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from']) && !empty($this->filters['date_to'])) {
            $query->whereBetween('order_items.created_at', [
                $this->filters['date_from'] . ' 00:00:00',
                $this->filters['date_to'] . ' 23:59:59',
            ]);
        } elseif (!empty($this->filters['date_from'])) {
            $query->where('order_items.created_at', '>=', $this->filters['date_from'] . ' 00:00:00');
        } elseif (!empty($this->filters['date_to'])) {
            $query->where('order_items.created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        return $query->orderBy('order_items.created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Product',
            'Variant',
            'Qty',
            'MRP',
            'Selling Price',
            'Discount',
            'Tax',
            'Sub Total',
            'Total',
            'Status',
            'Shipping Type',
            'Item Length',
            'Item Breadth',
            'Item Height',
            'Item Weight',
            'Customer Cancel Remark',
            'Admin Cancel Remark',
            'Customer Return Remark',
            'Admin Return Remark',
            'Status History',
            'Created On',
        ];
    }

    public function map($row): array
    {
        $cancelRequest = $row->cancelRequest;
        $refundRequest = $row->refundRequest;   
        $orderStatusHistory = $row->orderStatusHistory;
        $orderStatusHistoryArr = [];
        if(!empty($orderStatusHistory)){
            foreach($orderStatusHistory as $k=>$orderStatusHistoryItem) {
                $orderStatusHistoryArr[ucfirst($orderStatusHistoryItem->order_status)] = ["Remark"=>$orderStatusHistoryItem->remark,"Date"=>optional($orderStatusHistoryItem->created_at)->format('d M Y, h:i A')];
            }
        }
        $userCancelRemakr = $cancelRequest?->reason;
        $adminCancelRemakr = $cancelRequest?->admin_remark;
        $adminReturnRemakr = $refundRequest?->admin_remark;
        $userReturnRemakr = $refundRequest?->refund_reason;
        if($refundRequest?->refund_details){
            $userReturnRemakr .= ' ('.$refundRequest?->refund_details.')';
         }

        return [
            $row->order->order_number ?? '-',
            $row->product->name ?? '-',
            $row->combination ?? '-',
            $row->qty ?? 0,
            $row->mrp ?? 0,
            $row->selling_price ?? 0,
            $row->discount_amount ?? 0,
            $row->tax_amount ?? 0,
            $row->sub_total ?? 0,
            $row->total ?? 0,
            $row->orderStatus->name ?? ucfirst(str_replace('-', ' ', $row->status ?? '-')),
            $row->shipping_type ?? '-',
            $row->length ?? '-',
            $row->breadth ?? '-',
            $row->height ?? '-',
            $row->product->weight ?? '-',
            $userCancelRemakr ?? '-',
            $adminCancelRemakr ?? '-',
            $userReturnRemakr ?? '-',
            $adminReturnRemakr ?? '-',
            $orderStatusHistoryArr ? json_encode($orderStatusHistoryArr) : '-',
            optional($row->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}


