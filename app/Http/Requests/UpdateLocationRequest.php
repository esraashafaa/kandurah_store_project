<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{

    public function authorize(): bool
    {
        // التحقق من Policy: هل يملك المستخدم هذا الموقع؟
        $location = $this->route('location'); // الحصول على Location من Route
        
        return $this->user()->can('update', $location);
    }

    /**
     * قواعد الـ Validation
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // ═══════════════════════════════════════════════════════
            // 📍 ADDRESS FIELDS (كلها اختيارية في التحديث)
            // ═══════════════════════════════════════════════════════
            'city' => [
                'sometimes',  // اختياري - فقط إذا أُرسل
                'required',   // لكن إذا أُرسل، يجب ألا يكون فارغ
                'string',
                'max:100',
            ],

            'area' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],

            'street' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],

            'house_number' => [
                'sometimes',
                'required',
                'string',
                'max:50',
            ],

            // ═══════════════════════════════════════════════════════
            // 🌍 COORDINATES
            // ═══════════════════════════════════════════════════════
            'lat' => [
                'sometimes',
                'required',
                'numeric',
                'between:-90,90',
            ],

            'lng' => [
                'sometimes',
                'required',
                'numeric',
                'between:-180,180',
            ],

            // ═══════════════════════════════════════════════════════
            // ⭐ DEFAULT STATUS
            // ═══════════════════════════════════════════════════════
            'is_default' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * رسائل خطأ مخصصة
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'حقل :attribute مطلوب.',
            'string' => 'حقل :attribute يجب أن يكون نص.',
            'max' => 'حقل :attribute يجب ألا يتجاوز :max حرف.',
            'numeric' => 'حقل :attribute يجب أن يكون رقم.',
            'boolean' => 'حقل :attribute يجب أن يكون صحيح أو خطأ.',

            'city.required' => 'المدينة مطلوبة.',
            'area.required' => 'المنطقة مطلوبة.',
            'street.required' => 'اسم الشارع مطلوب.',
            'house_number.required' => 'رقم المنزل مطلوب.',

            'lat.between' => 'خط العرض يجب أن يكون بين -90 و 90.',
            'lng.between' => 'خط الطول يجب أن يكون بين -180 و 180.',
        ];
    }

    /**
     * أسماء الحقول بالعربية
     * 
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'city' => 'المدينة',
            'area' => 'المنطقة',
            'street' => 'الشارع',
            'house_number' => 'رقم المنزل',
            'lat' => 'خط العرض',
            'lng' => 'خط الطول',
            'is_default' => 'افتراضي',
        ];
    }

    /**
     * معالجة البيانات قبل الـ Validation
     * 
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // تنظيف البيانات
        if ($this->has('city')) {
            $this->merge(['city' => trim($this->city)]);
        }

        if ($this->has('area')) {
            $this->merge(['area' => trim($this->area)]);
        }

        if ($this->has('street')) {
            $this->merge(['street' => trim($this->street)]);
        }

        if ($this->has('is_default')) {
            $this->merge([
                'is_default' => filter_var($this->is_default, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}