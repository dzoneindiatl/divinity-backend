<?php
namespace App\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class CountryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    $id = $this->route('country') ? base64_decode($this->route('country')) : null;

    return [
        'name'              => 'required|string|max:255',
        'code'              => [
            'nullable',
            'string',
            'max:10',
            Rule::unique('countries', 'code')->ignore($id),
        ],
        'sortname'          => 'nullable|string|max:10',
        'country_flag'      => 'nullable|image|mimes:jpg,jpeg,png,gif,svg',
        'country_time_zone'  => 'nullable|string',
        'currency_symbol'   => 'nullable|string',
        'currency_amount'   => 'nullable|string',
    ];
}
}