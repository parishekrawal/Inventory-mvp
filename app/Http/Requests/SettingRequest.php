<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'company_name'=>'required|string|max:255',
            'logo'=>'nullable',
            'address'=>'nullable|string',
            'gst_number'=>'nullable|string|max:50',
            'invoice_prefix'=>'nullable|string|max:10',
            'financial_year_start'=>'nullable|date',
            'financial_year_end'=>'nullable|date',
        ];
    }
}
