<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    /**
     * هل المستخدم لديه صلاحية لهذا الطلب؟
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        // أي مستخدم مسجل يمكنه إنشاء موقع
        // يمكنك إضافة شروط إضافية هنا
        return auth()->check();
        
        // أو استخدام Policy:
        // return $this->user()->can('create', Location::class);
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
            // 📍 ADDRESS FIELDS
            // ═══════════════════════════════════════════════════════
            'city' => [
                'required',
                'string',
                'max:100',
                // يمكنك إضافة قائمة مدن مسموح بها
                // Rule::in(['القاهرة', 'الإسكندرية', 'الجيزة', ...]),
            ],

            'area' => [
                'required',
                'string',
                'max:100',
            ],

            'street' => [
                'required',
                'string',
                'max:150',
            ],

            'house_number' => [
                'required',
                'string',
                'max:50',
            ],

            // ═══════════════════════════════════════════════════════
            // 🌍 COORDINATES
            // ═══════════════════════════════════════════════════════
            'lat' => [
                'required',
                'numeric',
                'between:-90,90',  // نطاق Latitude
                // أو أكثر دقة:
                // 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',
            ],

            'lng' => [
                'required',
                'numeric',
                'between:-180,180',  // نطاق Longitude
                // أو أكثر دقة:
                // 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],

            // ═══════════════════════════════════════════════════════
            // ⭐ DEFAULT STATUS
            // ═══════════════════════════════════════════════════════
            'is_default' => [
                'sometimes',  // اختياري
                'boolean',
            ],
        ];
    }

    /**
     * رسائل خطأ مخصصة (اختياري)
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // رسائل عامة
            'required' => 'حقل :attribute مطلوب.',
            'string' => 'حقل :attribute يجب أن يكون نص.',
            'max' => 'حقل :attribute يجب ألا يتجاوز :max حرف.',
            'numeric' => 'حقل :attribute يجب أن يكون رقم.',
            'boolean' => 'حقل :attribute يجب أن يكون صحيح أو خطأ.',

            // رسائل خاصة بحقول معينة
            'city.required' => 'المدينة مطلوبة.',
            'city.max' => 'اسم المدينة طويل جداً.',
            
            'area.required' => 'المنطقة مطلوبة.',
            
            'street.required' => 'اسم الشارع مطلوب.',
            
            'house_number.required' => 'رقم المنزل مطلوب.',

            'lat.required' => 'خط العرض مطلوب.',
            'lat.between' => 'خط العرض يجب أن يكون بين -90 و 90.',
            
            'lng.required' => 'خط الطول مطلوب.',
            'lng.between' => 'خط الطول يجب أن يكون بين -180 و 180.',
        ];
    }

    /**
     * أسماء الحقول بالعربية (اختياري)
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
     * معالجة البيانات قبل الـ Validation (اختياري)
     * 
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // مثال: تنظيف البيانات قبل التحقق
        $this->merge([
            'city' => trim($this->city ?? ''),
            'area' => trim($this->area ?? ''),
            'street' => trim($this->street ?? ''),
            
            // تحويل is_default إلى boolean
            'is_default' => filter_var(
                $this->is_default ?? false, 
                FILTER_VALIDATE_BOOLEAN
            ),
        ]);
    }

    /**
     * معالجة البيانات بعد الـ Validation (اختياري)
     * 
     * @return array
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // مثال: إضافة user_id تلقائياً
        // $validated['user_id'] = auth()->id();

        return $validated;
    }

    /**
     * رسالة خطأ مخصصة عند فشل Authorization
     * 
     * @return void
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'You are not authorized to create a location.'
        );
    }
}