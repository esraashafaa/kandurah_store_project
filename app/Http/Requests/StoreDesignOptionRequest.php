<?php

namespace App\Http\Requests;

use App\Enums\DesignOptionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDesignOptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // سيتم التحقق من الصلاحيات في Middleware/Controller
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
            
            // النوع (مطلوب ويجب أن يكون من الأنواع المحددة)
            'type' => ['required', 'string', Rule::in(DesignOptionTypeEnum::values())],
            
            // حالة النشاط (اختياري، القيمة الافتراضية true)
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
            'type.required' => 'حقل النوع مطلوب',
            'type.in' => 'النوع المحدد غير صحيح',
        ];
    }
}
