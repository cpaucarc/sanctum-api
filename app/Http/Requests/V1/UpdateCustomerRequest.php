<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $method = $this->method();
        $customerTypes = config('constants.CustomerType');

        return $method == 'PUT'
            ? [
                'name' => ['required'],
                'type' => ['required', Rule::in(array_keys($customerTypes))],
                'email' => ['required', 'email'],
                'address' => ['required'],
                'city' => ['required'],
                'state' => ['required'],
                'postalCode' => ['required'],
            ]
            : [
                'name' => ['sometimes', 'required'],
                'type' => ['sometimes', 'required', Rule::in(array_keys($customerTypes))],
                'email' => ['sometimes', 'required', 'email'],
                'address' => ['sometimes', 'required'],
                'city' => ['sometimes', 'required'],
                'state' => ['sometimes', 'required'],
                'postalCode' => ['sometimes', 'required'],
            ];
    }

    protected function prepareForValidation()
    {
        if ($this->postalCode) {
            $this->merge(['postal_code' => $this->postalCode]);
        }

        if ($this->type) {
            $this->merge(['type' => strtoupper($this->type),]);
        }
    }
}
