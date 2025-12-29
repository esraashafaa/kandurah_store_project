<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $this->user()->can('updateStatus', $order);
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(OrderStatus::values()),
                function ($attribute, $value, $fail) {
                    $order = $this->route('order');
                    $newStatus = OrderStatus::from($value);
                    
                    if (!$order->status->canTransitionTo($newStatus)) {
                        $fail(__('لا يمكن الانتقال من :current_status إلى :new_status', [
                            'current_status' => $order->status->label(),
                            'new_status' => $newStatus->label(),
                        ]));
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => __('يجب تحديد الحالة الجديدة'),
            'status.in' => __('الحالة المحددة غير صالحة'),
        ];
    }
}