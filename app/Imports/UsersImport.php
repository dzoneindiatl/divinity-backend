<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UsersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows = $rows->slice(1)->toArray();
        foreach ($rows as $row) {
            // echo "<pre>"; print_r($row[3]); die;

            $originalString = $row[0] ?? "";
            $lowercaseString = Str::lower($originalString);
            $baseSlug = Str::slug($lowercaseString, '-');

            $alreadyAddedName = User::where('slug', $baseSlug)->first();

            if (!is_null($alreadyAddedName)) {
                $suffix = 1;
                while (User::where('slug', $baseSlug . '-' . $suffix)->exists()) {
                    $suffix++;
                }
                $slug = $baseSlug . '-' . $suffix;
            } else {
                $slug = $baseSlug;
            }

            $gender_value = isset($row[3]) ? Str::lower($row[3]) : "";

            $formattedDate = null;
            if (isset($row[4])) {
                $dateSerialNumber = $row[4];
                $unixTimestamp = ($dateSerialNumber - 25569) * 86400;
                $date = new \DateTime("@$unixTimestamp");
                $formattedDate = $date->format('Y-m-d');
            }

            $status_value = 0;
            $status = !empty($row[5]) ? Str::lower($row[5]) : "";
            if ($status == "active") {
                $status_value = 1;
            }



            $obj = new User;
            $obj->user_role_id = 2;
            $obj->slug = $slug ?? "";
            $obj->name = $row[0] ?? "";
            $obj->email = $row[1] ?? "";
            $obj->phone_number = $row[2] ?? "";
            $obj->gender = $gender_value;
            $obj->date_of_birth = $formattedDate;
            $obj->is_active = $status_value;
            $obj->created_at = Carbon::now();
            $obj->updated_at = Carbon::now();
            // echo "<pre>"; print_r($obj);
            $obj->save();
        }
        // return true;
    }
}
