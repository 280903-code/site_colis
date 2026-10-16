<?php

namespace App\Http\Requests;

use App\Support\Country;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PartnerRegistrationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        if (is_string($email)) {
            $this->merge(['email' => mb_strtolower(trim($email))]);
        }

        $whatsapp = $this->input('whatsapp');

        if (is_string($whatsapp)) {
            $this->merge([
                'whatsapp' => preg_replace('/[^0-9]/', '', $whatsapp),
            ]);
        }

        $routes = $this->input('routes');

        if (is_array($routes)) {
            foreach ($routes as $index => $route) {
                if (is_string($route)) {
                    $decodedRoute = json_decode($route, true);

                    if (is_array($decodedRoute)) {
                        $routes[$index] = $decodedRoute;
                    }
                }

                if (is_array($routes[$index])) {
                    foreach (['from', 'to'] as $field) {
                        if (isset($routes[$index][$field]) && is_string($routes[$index][$field])) {
                            $routes[$index][$field] = Country::normalize($routes[$index][$field]);
                        }
                    }
                }
            }

            $this->merge(['routes' => $routes]);
        }
    }

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
            'routes.*.from' => 'required|string|max:100',
            'routes.*.to' => 'required|string|max:100|different:routes.*.from',
            'website' => 'nullable|max:255', // Honeypot field
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp.regex' => 'Le numéro WhatsApp ne doit contenir que des chiffres.',
            'routes.*.to.different' => 'Le pays de destination doit être différent du pays de départ.',
        ];
    }
}
