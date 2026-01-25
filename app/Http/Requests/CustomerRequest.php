<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:30',
            'phone' => 'required|string|unique:customers,phone,'.$this->route('id'),
            'address' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string',
            'opening_balance' => 'nullable'
        ];
    }
}
