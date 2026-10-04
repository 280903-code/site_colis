<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PartnerRegistrationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'agency_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'opening_hours' => 'required|string|max:255',
            'whatsapp' => 'required|string|regex:/^[0-9]+$/|min:10|max:15',
            'routes' => 'required|array|min:1',
            'routes.*' => 'array',
            'routes.*.from' => 'required|in:SN,KM,FR',
            'routes.*.to' => 'required|in:SN,KM,FR|different:routes.*.from',
            'website' => 'nullable|max:255', // Honeypot field
        ];
    }

    protected function prepareForValidation()
    {
        // Clean WhatsApp - keep only digits
        if ($this->has('whatsapp')) {
            $this->merge([
                'whatsapp' => preg_replace('/[^0-9]/', '', $this->whatsapp),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'whatsapp.regex' => 'Le numéro WhatsApp ne doit contenir que des chiffres.',
            'routes.*.to.different' => 'Le pays de destination doit être différent du pays de départ.',
        ];
    }
}
