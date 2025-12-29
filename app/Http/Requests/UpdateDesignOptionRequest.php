<?php

namespace App\Http\Requests;

use App\Enums\DesignOptionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDesignOptionRequest extends FormRequest
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
            // الاسم (اختياري في التحديث)
            'name' => ['sometimes', 'required', 'array'],
            'name.en' => ['required_with:name', 'string', 'max:255'],
            'name.ar' => ['required_with:name', 'string', 'max:255'],
            
            // النوع (اختياري في التحديث)
            'type' => ['sometimes', 'required', 'string', Rule::in(DesignOptionTypeEnum::values())],
            
            // حالة النشاط (اختياري)
            'is_active' => ['sometimes', 'boolean'],
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
            'type.required' => 'حقل النوع مطلوب',
            'type.in' => 'النوع المحدد غير صحيح',
        ];
    }
}
