<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // سيتم التحقق من الصلاحيات في Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // الاسم (اختياري في التحديث)
            'name' => ['sometimes', 'required', 'array'],
            'name.en' => ['required_with:name', 'string', 'max:255'],
            'name.ar' => ['required_with:name', 'string', 'max:255'],
            
            // الوصف (اختياري في التحديث)
            'description' => ['sometimes', 'required', 'array'],
            'description.en' => ['required_with:description', 'string', 'max:2000'],
            'description.ar' => ['required_with:description', 'string', 'max:2000'],
            
            // السعر (اختياري في التحديث)
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999.99'],
            
            // الصور (اختياري في التحديث)
            'images' => ['sometimes', 'array'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            
            // المقاسات (اختياري في التحديث)
            'size_ids' => ['sometimes', 'array', 'min:1'],
            'size_ids.*' => ['required', 'integer', 'exists:sizes,id'],
            
            // خيارات التصميم (اختياري)
            'design_option_ids' => ['sometimes', 'array'],
            'design_option_ids.*' => ['required', 'integer', 'exists:design_options,id'],
            
            // حالة النشر (اختياري)
            'is_active' => ['sometimes', 'boolean'],
            
            // الاحتفاظ بالصور القديمة عند رفع صور جديدة
            'keep_existing_images' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * رسائل الأخطاء المخصصة
     */
    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب',
            'name.en.required_with' => 'الاسم بالإنجليزية مطلوب',
            'name.ar.required_with' => 'الاسم بالعربية مطلوب',
            'description.required' => 'حقل الوصف مطلوب',
            'description.en.required_with' => 'الوصف بالإنجليزية مطلوب',
            'description.ar.required_with' => 'الوصف بالعربية مطلوب',
            'price.required' => 'حقل السعر مطلوب',
            'price.numeric' => 'السعر يجب أن يكون رقماً',
            'price.min' => 'السعر يجب أن يكون أكبر من أو يساوي 0',
            'images.*.image' => 'الملف يجب أن يكون صورة',
            'images.*.mimes' => 'الصورة يجب أن تكون من نوع: jpeg, png, jpg, gif, webp',
            'images.*.max' => 'حجم الصورة يجب ألا يتجاوز 5 ميجابايت',
            'size_ids.min' => 'يجب اختيار مقاس واحد على الأقل',
            'size_ids.*.exists' => 'المقاس المحدد غير موجود',
            'design_option_ids.*.exists' => 'خيار التصميم المحدد غير موجود',
        ];
    }
}
