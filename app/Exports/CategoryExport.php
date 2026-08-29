<?php

namespace App\Exports;

use App\Models\Tax;
use App\Models\User;
use App\Models\Variant;
use App\Models\Category;
use App\Models\Attribute;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CategoryExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    protected $selects;
    protected $rowCount;
    protected $columnCount;

    public function __construct()
    {
        // Retrieve predefined options for dropdowns
        $category_type_id = ['Category', 'Collection'];

        // Define columns and their respective dropdown options
        $this->selects = [
            ['columns_name' => 'D', 'options' => $category_type_id],
        ];
        $this->rowCount = Category::where('is_deleted', 0)->count() + 1;
        $this->columnCount = 6;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $users = Category::where('is_deleted', 0)->get();
        return $users;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Category Type',
            'image',
            'Thumbnail Image',
            'video',
            'show_on_home',
            'Tax',
            'Varition',
            'Attributes',
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
        $categoryTxa = "";
        $categoryVarint = "";
        $categoryattributedtaa = "";

        foreach ($user->taxes as $addr) {
            $cats = Tax::where('id', $addr->tax_id)->get();

            if ($cats->isNotEmpty()) {
                foreach ($cats as $cat) {
                    $categoryTxa .= $addr->tax_option . ', ' . $addr->tax_type . ', ' .
                        $cat->tax_from . ', ' . $cat->tax_to . ', ' . $cat->tax_rate . ' | ';
                }
            }
        }

        foreach ($user->variants as $category) {
            $variant = Variant::with('variant_values')->find($category->variant_id);

            if ($variant) {
                $values = $variant->variant_values->pluck('name')->implode(',');
                $categoryVarint .= $variant->name . '(' . $values . ')' . ',' . "\n";
            }
        }

        foreach ($user->attributes as $attr) {
            $attribute = Attribute::with('attribute_value')->find($attr->attribute_id);

            if ($attribute) {
                $values = $attribute->attribute_value->pluck('name')->implode(',');
                $categoryattributedtaa .= $attribute->name . '(' . $values . ')' . ',' . "\n";
            }
        }
        $category_type = $user->category_type_id == 2 ? "Category" : "Collection";
        return [
            $user->name ?? null,
            $category_type,
            $user->image ?? null,
            $user->thumbnail_image ?? null,
            $user->video ?? null,
            $user->show_on_home ?? null,
            $categoryTxa,
            $categoryVarint,
            $categoryattributedtaa

        ];
    }
}