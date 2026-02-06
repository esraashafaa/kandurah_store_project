<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeasurementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'chest' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'waist' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sleeve' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'shoulder' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'hip' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم القياس مطلوب',
            'name.string' => 'اسم القياس يجب أن يكون نصاً',
            'name.max' => 'اسم القياس يجب ألا يتجاوز 255 حرفاً',
            'chest.numeric' => 'الصدر يجب أن يكون رقماً',
            'waist.numeric' => 'الخصر يجب أن يكون رقماً',
            'sleeve.numeric' => 'الأكمام يجب أن تكون رقماً',
            'shoulder.numeric' => 'الكتف يجب أن يكون رقماً',
            'hip.numeric' => 'الورك يجب أن يكون رقماً',
            'height.numeric' => 'الطول يجب أن يكون رقماً',
        ];
    }
}
