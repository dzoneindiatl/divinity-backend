<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Coupon;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CouponExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    protected $selects;
    protected $rowCount;
    protected $columnCount;

    public function __construct()
    {
        // Retrieve predefined options for dropdowns
        $genders = ['Male', 'Female', 'Other'];

        // Define columns and their respective dropdown options
        $this->selects = [
            ['columns_name' => 'D', 'options' => $genders],
        ];
        $this->rowCount = Coupon::where('is_active', 1)->count() + 1; // Adding 1 for the header row
        $this->columnCount = 6; // Number of columns in the Excel sheet
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $users = Coupon::where('is_active', 1)->get();
        return $users;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Coupon Code',
            'Total Use',
            'Total Available',
            'Per User Available Coupon',
            'User type',
            'Coupon Type',
            'Discount Type',
            'Discount Value',
            'Start Date',
            'End Date',
            'Min Cart Value',
            'Max Discount',
            'Show On Detail',
            'Free Shipping'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $row_count = $this->rowCount;
                $column_count = $this->columnCount;
                $hiddenSheet = $event->sheet->getDelegate()->getParent()->createSheet();
                $hiddenSheet->setTitle('Hidden');
                $hiddenSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

                foreach ($this->selects as $select) {
                    $drop_column = $select['columns_name'];
                    $options = $select['options'];

                    // Populate hidden sheet with dropdown values
                    foreach ($options as $index => $option) {
                        $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(1) . ($index + 1);
                        $hiddenSheet->setCellValue($cellCoordinate, $option);
                    }

                    // Set data validation formula to refer to hidden sheet cells
                    $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1('Hidden!$A$1:$A$' . count($options));

                    // Clone validation to remaining rows
                    for ($i = 2; $i <= $row_count; $i++) {
                        $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                    }

                    // Set columns to autosize
                    for ($i = 1; $i <= $column_count; $i++) {
                        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                        $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            },
        ];
    }

    public function map($user): array
    {
        $status = $user->show_on_detail == 1 ? "Yes" : "No";
        $has_free_shipping = $user->has_free_shipping == 1 ? "Yes" : "No";
        $couponUses = $user?->couponUses?->count() ?? 0;
        return [
            $user->name ?? null,
            $user->coupon_code ?? null,
            $couponUses ?? null,
            $user->available_coupons ?? null,
            $user->per_user_avalibity ?? null,
            $user->user_type ?? null,
            $user->coupon_type ?? null,
            $user->discount_type ?? null,
            $user->discount_value ?? null,
            $user->start_date ?? null,
            $user->end_date ?? null,
            $user->min_cart_value ?? null,
            $user->max_discount ?? null,
            $status,
            $has_free_shipping,


        ];
    }
}
