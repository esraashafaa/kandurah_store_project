<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignRequest extends FormRequest
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
            // الاسم (مطلوب وقابل للترجمة)
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            
            // الوصف (مطلوب وقابل للترجمة)
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string', 'max:2000'],
            'description.ar' => ['required', 'string', 'max:2000'],
            
            // السعر (مطلوب وموجب)
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            
            // الصور (مطلوب صورة واحدة على الأقل)
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB max
            
            // المقاسات (مطلوب مقاس واحد على الأقل)
            'size_ids' => ['required', 'array', 'min:1'],
            'size_ids.*' => ['required', 'integer', 'exists:sizes,id'],
            
            // خيارات التصميم (اختياري)
            'design_option_ids' => ['nullable', 'array'],
            'design_option_ids.*' => ['required', 'integer', 'exists:design_options,id'],
            
            // حالة النشر (اختياري، القيمة الافتراضية true)
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * رسائل الأخطاء المخصصة
     */
    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب',
            'name.en.required' => 'الاسم بالإنجليزية مطلوب',
            'name.ar.required' => 'الاسم بالعربية مطلوب',
            'description.required' => 'حقل الوصف مطلوب',
            'description.en.required' => 'الوصف بالإنجليزية مطلوب',
            'description.ar.required' => 'الوصف بالعربية مطلوب',
            'price.required' => 'حقل السعر مطلوب',
            'price.numeric' => 'السعر يجب أن يكون رقماً',
            'price.min' => 'السعر يجب أن يكون أكبر من أو يساوي 0',
            'images.required' => 'يجب رفع صورة واحدة على الأقل',
            'images.min' => 'يجب رفع صورة واحدة على الأقل',
            'images.*.image' => 'الملف يجب أن يكون صورة',
            'images.*.mimes' => 'الصورة يجب أن تكون من نوع: jpeg, png, jpg, gif, webp',
            'images.*.max' => 'حجم الصورة يجب ألا يتجاوز 5 ميجابايت',
            'size_ids.required' => 'يجب اختيار مقاس واحد على الأقل',
            'size_ids.min' => 'يجب اختيار مقاس واحد على الأقل',
            'size_ids.*.exists' => 'المقاس المحدد غير موجود',
            'design_option_ids.*.exists' => 'خيار التصميم المحدد غير موجود',
        ];
    }
}
