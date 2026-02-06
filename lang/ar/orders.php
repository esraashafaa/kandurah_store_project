<?php

return [
    'status' => [
        'pending' => 'قيد الانتظار',
        'confirmed' => 'مؤكد',
        'processing' => 'قيد التجهيز',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
        'refunded' => 'مسترد',
        'unknown' => 'غير محدد',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التوصيل',
    ],
    
    'payment_methods' => [
        'cash' => 'نقداً',
        'wallet' => 'المحفظة',
        'card' => 'بطاقة ائتمان',
        'bank_transfer' => 'تحويل بنكي',
        'stripe' => 'Stripe',
    ],
    
    'payment_status' => [
        'pending' => 'قيد الانتظار',
        'paid' => 'مدفوع',
        'failed' => 'فشل',
        'refunded' => 'مسترد',
    ],
];

