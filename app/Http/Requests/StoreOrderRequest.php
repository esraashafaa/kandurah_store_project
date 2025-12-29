<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Order::class);
    }

    public function rules(): array
    {
        return [
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
                // التحقق من أن العنوان يخص المستخدم نفسه
                function ($attribute, $value, $fail) {
                    $location = \App\Models\Location::find($value);
                    if (!$location || $location->user_id !== $this->user()->id) {
                        $fail(__('العنوان المحدد غير صالح'));
                    }
                },
            ],
            'notes' => 'nullable|string|max:1000',
            
            // دعم إنشاء الطلب مباشرة من items (بدون cart)
            'items' => 'sometimes|array|min:1',
            'items.*.design_id' => 'required_with:items|integer|exists:designs,id',
            'items.*.size_id' => 'required_with:items|integer|exists:sizes,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.design_option_ids' => 'nullable|array',
            'items.*.design_option_ids.*' => 'integer|exists:design_options,id',
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => __('يجب اختيار عنوان الشحن'),
            'location_id.exists' => __('العنوان المحدد غير موجود'),
            'notes.max' => __('الملاحظات يجب أن لا تتجاوز 1000 حرف'),
        ];
    }
}