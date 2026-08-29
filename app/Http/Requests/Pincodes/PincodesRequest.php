<?php
namespace App\Http\Requests\Pincodes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class PincodesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    $id = $this->route('pincodes') ? base64_decode($this->route('pincodes')) : null;

    return [
        // 'pincode'  => 'nullable|string',
        // 'delivery'  => 'nullable|string',
        // 'extra_charge'  => 'nullable|string',
        'city_id'  => 'nullable|string',
        'state_id'  => 'nullable|string',
        'country_id'  => 'nullable|string',
        // 'status'  => 'nullable|string',
     ];
}
}