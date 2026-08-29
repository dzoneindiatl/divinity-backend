<?php

namespace App\Exports;

use App\Models\User;
use App\Models\WalletHistory;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithEvents
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
        $this->rowCount = User::where('is_deleted', 0)->count() + 1; // Adding 1 for the header row
        $this->columnCount = 6; // Number of columns in the Excel sheet
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $users = User::where('is_deleted', 0)->where('user_role_id', 3)->get();
        return $users;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Login Type',
            'Refferal Code',
            'Refferal',
            'Total Balance',
            'Total Redeemed',
            'Total Earn From Refferal',
            'Total Earn From Refound',
            'Phone Number',
            'Gender',
            'Date Of Birth',
            'Address',
            'Status',
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

                /* foreach ($this->selects as $select) {
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
                } */
            },
        ];
    }

    public function map($user): array
    {
        $userAddress = "";
        $totalReffreal = "";
        foreach ($user->address as $addr) {
            if ($addr->is_primary == 1) {
                $userAddress = $addr->address . ', ' . $addr->postal_code . ', ' . $addr->city->name . ', ' . $addr->state->name . ', ' . $addr->country->name;
            } else {
                $userAddress = $addr->address . ', ' . $addr->postal_code . ', ' . $addr->city->name . ', ' . $addr->state->name . ', ' . $addr->country->name;
            }
        }
        $totalReffreal = count($user->referralhistorys);

        $status = $user->is_active == 1 ? "Active" : "Inactive";
        return [
            $user->name ?? null,
            $user->email ?? null,
            ucfirst($user->login_type) ?? 'Normal',
            $user->referral_code ?? null,
            $totalReffreal,
            $user->wallet_avl_balance,
            $user->debit_total_amount,
            $user->referral_wallet,
            $user->refundCreditHistorys()->sum('amount'),
            $user->phone_number ?? null,
            ucfirst($user->gender) ?? null,
            $user->date_of_birth ?? null,
            $userAddress ?? null,
            $status,
        ];
    }
}