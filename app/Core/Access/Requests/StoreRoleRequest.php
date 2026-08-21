<?php

namespace App\Core\Access\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ];
    }
}