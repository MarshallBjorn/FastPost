<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all for now. Use auth logic if needed.
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id; // Only present on update

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-\(\)]{7,20}$/',
            ],

            // Staff stuff
            'staff_type' => ['nullable', 'in:admin,courier,warehouse', 'required_with:hire_date'],
            'hire_date' => ['nullable', 'date', 'required_with:staff_type'],
            'termination_date' => ['nullable', 'date', 'after_or_equal:hire_date'],
        ];

        if ($this->isMethod('post')) {
            // Creating: require password
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Updating: password optional
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }
}
