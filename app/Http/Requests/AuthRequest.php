<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        switch($this->route()->getName()){
            case 'login':
                return [
                        'email'=>'required|email',
                        'password'=>'required|string|min:6',
                ];
            
            case 'changePassword':
                return [
                    'current_password' => 'required',
                    'new_password' => 'required|string|min:6|confirmed',
                ];

            case 'forgotPassword':
                return [
                    'email' => 'required|email',
                ];
            
            case 'resetPassword':
                return [
                    'email'=>'required|email',
                    'token'=>'required',
                    'password'=>'required|string|min:6|confirmed'
                ];
                
            default:
                return [];
        }
    }
}
