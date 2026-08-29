<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $route = Route::currentRouteName();
        if ($route == 'admin-verify-login') {
            return [
                'email' => 'required|exists:users,email',
                'password' => 'required|min:6',
            ];
        }
    }

    protected function failedValidation(Validator $validator)
    {     
        throw new HttpResponseException(redirect()->back()->withErrors($validator)->withInput());
    }
}
