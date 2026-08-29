<?php
namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class CityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    $id = $this->route('city') ? base64_decode($this->route('city')) : null;

    return [
        'name'              => 'required|string|max:255',
        'postal_code'              => [
            'nullable',
            'string',
            'max:10',
            Rule::unique('cities', 'postal_code')->ignore($id),
        ],
        'short_name'          => 'nullable|string|max:10',
        'std_code'  => 'nullable|string',
    ];
}
}