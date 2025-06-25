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

    protected function prepareForValidation()
    {
        if ($this->filled('password') && $this->input('password') == "") {
            $this->merge(['password' => null]);
        }
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // dd($validator->errors()->all());
    }

    public function messages()
    {
        return [
            'staff.warehouse_id.exists' => 'The selected warehouse is invalid or does not exist.',
            'staff.termination_date.after' => 'The termination date must be after the hire date.',
        ];
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        if ($this->boolean('_profile_update')) {
            $userId = auth()->id();
        }

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
            'staff.staff_type' => ['nullable', 'in:admin,courier,warehouse', 'required_with:hire_date,warehouse_id'],
            'staff.hire_date' => ['nullable', 'date', 'required_with:staff_type'],
            'staff.warehouse_id' => ['nullable', 'exists:warehouses,id'],
            // Unused
            'staff.termination_date' => ['nullable', 'date', 'after_or_equal:hire_date'],
        ];

        if ($this->boolean('_profile_update')) {
            // Optional password for profile updates
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        } elseif ($this->isMethod('post')) {
            // Creating: require password
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Updating: password optional
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $staff = $this->input('staff', []);

            if (!empty($staff['warehouse_id'])) {
                if (empty($staff['staff_type'])) {
                    $validator->errors()->add('staff_type', 'Staff type is required if warehouse is set.');
                }
                if (empty($staff['hire_date'])) {
                    $validator->errors()->add('hire_date', 'Hire date is required if warehouse is set.');
                }
            }
        });
    }
}
