<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    protected $filters;
    protected $srNo = 1;
    protected $columnCount = 28; // Total columns in export

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::query();
        $searchData = $this->filters;

        if (!empty($searchData['date_from']) && !empty($searchData['date_to'])) {
            $query->whereBetween('created_at', [
                $searchData['date_from'] . " 00:00:00",
                $searchData['date_to'] . " 23:59:59"
            ]);
        } elseif (!empty($searchData['date_from'])) {
            $query->where('created_at', '>=', $searchData['date_from'] . " 00:00:00");
        } elseif (!empty($searchData['date_to'])) {
            $query->where('created_at', '<=', $searchData['date_to'] . " 23:59:59");
        }

        if (!empty($searchData['name'])) {
            $query->where("name", 'like', '%' . $searchData['name'] . '%');
        }

        if (!empty($searchData['category_id'])) {
            $query->where("main_category_id", $searchData['category_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Sr No.',
            'Name',
            'SKU',
            'HSN',
            'Weight',
            'Wash Care',
            'Product Number',
            'Main Category',
            'Main Sub Category',
            'Main Child Category',
            'Brand',
            'Short Description',
            'Buying Price',
            'Quantity',
            'Discount',
            'Discount Type',
            'Selling Price',
            'Is Including Tax',
            'In Stock',
            'Is Featured',
            'Is New Arrival',
            'Is New',
            'Trending',
            'Best Selling',
            'Best Seller',
            'Draft',
            'Status',
        ];
    }

    public function map($product): array
    {
        return [
            $this->srNo++,
            $product->name ?? '',
            $product->sku ?? '',
            $product->hsn ?? '',
            $product->weight ? $product->weight . ' ' . ($product->weight_type ?? 'grm') : '',
            $product->wash_care ?? '',
            $product->product_number ?? '',
            $product->mainCategory->name ?? '',
            $product->mainSubCategory->name ?? '',
            $product->mainChildCategory->name ?? '',
            $product->brand->name ?? '',
            $product->short_description ?? '',
            $product->buying_price ?? '',
            $product->qty ?? '',
            $product->discount ?? '',
            $product->discount_type ?? '',
            $product->selling_price ?? '',
            $product->is_including_taxes ? 'Yes' : 'No',
            $product->in_stock ? 'Yes' : 'No',
            $product->is_featured ? 'Yes' : 'No',
            $product->is_new_arrivals ? 'Yes' : 'No',
            $product->is_new ? 'Yes' : 'No',
            $product->trending ? 'Yes' : 'No',
            $product->best_selling ? 'Yes' : 'No',
            $product->best_seller ? 'Yes' : 'No',
            $product->draf ? 'Draft' : 'Publish',
            $product->is_active ? 'Active' : 'Inactive',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Header styling
                $columnCount = $this->columnCount;
                $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount) . '1';

                $event->sheet->getStyle($headerRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFF00'], // Yellow
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => '000000'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Auto-size all columns
                for ($i = 1; $i <= $columnCount; $i++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
