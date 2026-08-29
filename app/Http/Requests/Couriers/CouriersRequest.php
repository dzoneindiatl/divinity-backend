<?php
namespace App\Http\Requests\Couriers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class CouriersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    $id = $this->route('couriers') ? base64_decode($this->route('couriers')) : null;

    return [
        'name'              => 'required|string|max:255',
        'code'              => [
            'nullable',
            'string',
            'max:10',
            Rule::unique('countries', 'code')->ignore($id),
        ],
        'name' => 'nullable|string|max:10|unique:couriers,name',
        'tracking_url'  => 'nullable|string',
    ];
}
}