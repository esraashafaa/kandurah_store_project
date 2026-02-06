<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Design;
use App\Services\CouponService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        private CouponService $couponService
    ) {}
    /**
     * إنشاء طلب جديد من السلة
     * 
     * @param int $userId
     * @param int $locationId
     * @param array $cartItems من CartService::getItems()
     * @param string|null $notes
     * @param string|null $couponCode
     * @return Order
     */
    public function createOrderFromCart(
        int $userId,
        int $locationId,
        array $cartItems,
        ?string $notes = null,
        ?string $couponCode = null
    ): Order {
        
        if (empty($cartItems)) {
            throw new \Exception('السلة فارغة');
        }

        return DB::transaction(function () use ($userId, $locationId, $cartItems, $notes, $couponCode) {
            
            // 1. حساب المجموع الكلي
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['subtotal'];
            }

            // 2. تطبيق الكوبون إن وجد
            $couponId = null;
            $discountAmount = 0;
            $totalAmount = $subtotal;

            if ($couponCode) {
                try {
                    $couponResult = $this->couponService->applyCoupon($couponCode, $subtotal, $userId);
                    $couponId = $couponResult['coupon']->id;
                    $discountAmount = $couponResult['discount_amount'];
                    $totalAmount = $couponResult['final_amount'];
                } catch (\Exception $e) {
                    // إذا فشل تطبيق الكوبون، نرمي الاستثناء
                    Log::warning('Failed to apply coupon', [
                        'coupon_code' => $couponCode,
                        'user_id' => $userId,
                        'error' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }

            // 3. إنشاء الطلب الرئيسي في جدول orders
            $order = Order::create([
                'user_id' => $userId,
                'location_id' => $locationId,
                'coupon_id' => $couponId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => \App\Enums\OrderStatus::PENDING,
                'notes' => $notes,
            ]);

            // التحقق من إنشاء الطلب بنجاح
            if (!$order || !$order->id) {
                throw new \Exception('فشل إنشاء الطلب في جدول orders');
            }

            // 3. إضافة عناصر الطلب في جدول order_items
            $createdItems = [];
            foreach ($cartItems as $item) {
                $selectedOptions = $item['selected_options'] ?? [];
                $sizeId = $selectedOptions['size_id'] ?? null;
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'design_id' => $item['design']['id'],
                    'size_id' => $sizeId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'], // السعر وقت الطلب
                    'selected_options' => $selectedOptions,
                ]);

                // التحقق من إنشاء عنصر الطلب بنجاح
                if (!$orderItem || !$orderItem->id) {
                    throw new \Exception('فشل إنشاء عنصر الطلب في جدول order_items');
                }

                $createdItems[] = $orderItem;
            }

            // التحقق من أن جميع العناصر تم إنشاؤها
            if (count($createdItems) !== count($cartItems)) {
                throw new \Exception('لم يتم إنشاء جميع عناصر الطلب في جدول order_items');
            }

            // 4. تسجيل استخدام الكوبون
            if ($couponId) {
                $coupon = \App\Models\Coupon::find($couponId);
                if ($coupon) {
                    $this->couponService->recordUsage($coupon, $userId, $order->id);
                }
            }

            // 5. Log للمتابعة
            Log::info('Order created from cart', [
                'order_id' => $order->id,
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'coupon_id' => $couponId,
                'items_count' => count($cartItems),
                'created_items_count' => count($createdItems),
            ]);

            return $order->load(['items.design', 'items.size', 'location', 'user', 'coupon']);
        });
    }

    /**
     * إنشاء طلب جديد مباشرة من items
     * 
     * @param int $userId
     * @param int $locationId
     * @param array $items [['design_id' => 1, 'size_id' => 2, 'quantity' => 1, 'design_option_ids' => [1,2,3]]]
     * @param string|null $notes
     * @param string|null $couponCode
     * @return Order
     */
    public function createOrderFromItems(
        int $userId,
        int $locationId,
        array $items,
        ?string $notes = null,
        ?string $couponCode = null
    ): Order {
        
        if (empty($items)) {
            throw new \Exception('يجب إضافة عنصر واحد على الأقل');
        }

        return DB::transaction(function () use ($userId, $locationId, $items, $notes, $couponCode) {
            
            // 1. حساب المجموع الكلي وإنشاء عناصر الطلب
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $design = Design::findOrFail($item['design_id']);
                $quantity = $item['quantity'] ?? 1;
                $price = $design->price;
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;

                // تجهيز selected_options
                $selectedOptions = [
                    'size_id' => $item['size_id'] ?? null,
                    'design_option_ids' => $item['design_option_ids'] ?? [],
                ];

                $orderItems[] = [
                    'design_id' => $design->id,
                    'size_id' => $item['size_id'] ?? null,
                    'quantity' => $quantity,
                    'price' => $price,
                    'selected_options' => $selectedOptions,
                ];
            }

            // 2. تطبيق الكوبون إن وجد
            $couponId = null;
            $discountAmount = 0;
            $totalAmount = $subtotal;

            if ($couponCode) {
                try {
                    $couponResult = $this->couponService->applyCoupon($couponCode, $subtotal, $userId);
                    $couponId = $couponResult['coupon']->id;
                    $discountAmount = $couponResult['discount_amount'];
                    $totalAmount = $couponResult['final_amount'];
                } catch (\Exception $e) {
                    // إذا فشل تطبيق الكوبون، نرمي الاستثناء
                    Log::warning('Failed to apply coupon', [
                        'coupon_code' => $couponCode,
                        'user_id' => $userId,
                        'error' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }

            // 3. إنشاء الطلب الرئيسي في جدول orders
            $order = Order::create([
                'user_id' => $userId,
                'location_id' => $locationId,
                'coupon_id' => $couponId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => \App\Enums\OrderStatus::PENDING,
                'notes' => $notes,
            ]);

            // التحقق من إنشاء الطلب بنجاح
            if (!$order || !$order->id) {
                throw new \Exception('فشل إنشاء الطلب في جدول orders');
            }

            // 3. إضافة عناصر الطلب في جدول order_items
            $createdItems = [];
            foreach ($orderItems as $itemData) {
                $orderItem = OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));
                
                // التحقق من إنشاء عنصر الطلب بنجاح
                if (!$orderItem || !$orderItem->id) {
                    throw new \Exception('فشل إنشاء عنصر الطلب في جدول order_items');
                }

                $createdItems[] = $orderItem;
            }

            // التحقق من أن جميع العناصر تم إنشاؤها
            if (count($createdItems) !== count($items)) {
                throw new \Exception('لم يتم إنشاء جميع عناصر الطلب في جدول order_items');
            }

            // 4. تسجيل استخدام الكوبون
            if ($couponId) {
                $coupon = \App\Models\Coupon::find($couponId);
                if ($coupon) {
                    $this->couponService->recordUsage($coupon, $userId, $order->id);
                }
            }

            // 5. Log للمتابعة
            Log::info('Order created from items', [
                'order_id' => $order->id,
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'coupon_id' => $couponId,
                'items_count' => count($items),
                'created_items_count' => count($createdItems),
            ]);

            return $order->load(['items.design.images', 'items.size', 'location', 'user', 'coupon']);
        });
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(Order $order, string|\App\Enums\OrderStatus $newStatus): Order
    {
        // تحويل string إلى OrderStatus إذا لزم الأمر
        // إذا كان $newStatus هو OrderStatus enum بالفعل، نتركه كما هو
        if (is_string($newStatus)) {
            $newStatus = \App\Enums\OrderStatus::from($newStatus);
        }
        
        // التأكد من أن $newStatus هو OrderStatus enum
        if (!$newStatus instanceof \App\Enums\OrderStatus) {
            throw new \Exception('حالة الطلب غير صحيحة');
        }

        // التحقق من إمكانية الانتقال
        if (!$order->status->canTransitionTo($newStatus)) {
            throw new \Exception('لا يمكن الانتقال من ' . $order->status->label() . ' إلى ' . $newStatus->label());
        }

        $oldStatus = $order->status;
        // تحديث الحالة باستخدام save() لضمان أن Observer يعمل بشكل صحيح
        $order->status = $newStatus;
        $order->save();

        Log::info('Order status updated', [
            'order_id' => $order->id,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
        ]);

        // تحميل العلاقات المطلوبة قبل الإرجاع
        return $order->fresh()->load(['user', 'location', 'items']);
    }

    /**
     * إلغاء الطلب
     */
    public function cancelOrder(Order $order): Order
    {
        if (!$order->canBeCancelled()) {
            throw new \Exception('لا يمكن إلغاء هذا الطلب');
        }

        return $this->updateStatus($order, \App\Enums\OrderStatus::CANCELLED);
    }

    /**
     * الحصول على طلبات المستخدم مع الفلترة
     */
    public function getUserOrders(
        int $userId,
        ?string $search = null,
        ?string $status = null,
        ?string $sortBy = null,
        ?string $sortDir = null,
        int $perPage = 15
    ) {
        return Order::where('user_id', $userId)
            ->with(['items.design.images', 'location'])
            ->withCount('items')
            ->search($search)
            ->status($status)
            ->sort($sortBy, $sortDir)
            ->paginate($perPage);
    }

    /**
     * الحصول على جميع الطلبات للأدمن مع الفلترة
     */
    public function getAllOrders(
        ?string $search = null,
        ?string $status = null,
        ?int $userId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $sortBy = null,
        ?string $sortDir = null,
        int $perPage = 15
    ) {
        return Order::with(['user', 'items.design.images', 'location', 'invoice'])
            ->withCount('items')
            ->search($search)
            ->status($status)
            ->forUser($userId)
            ->priceRange($minPrice, $maxPrice)
            ->dateRange($startDate, $endDate)
            ->sort($sortBy, $sortDir)
            ->paginate($perPage);
    }

    /**
     * إحصائيات سريعة للأدمن
     */
    public function getStatistics(): array
    {
        return [
            'total' => Order::count(),
            'pending' => Order::where('status', \App\Enums\OrderStatus::PENDING->value)->count(),
            'confirmed' => Order::where('status', \App\Enums\OrderStatus::CONFIRMED->value)->count(),
            'processing' => Order::where('status', \App\Enums\OrderStatus::PROCESSING->value)->count(),
            'shipped' => Order::where('status', \App\Enums\OrderStatus::SHIPPED->value)->count(),
            'delivered' => Order::where('status', \App\Enums\OrderStatus::DELIVERED->value)->count(),
            'completed' => Order::whereIn('status', [
                \App\Enums\OrderStatus::DELIVERED->value,
            ])->count(),
            'cancelled' => Order::where('status', \App\Enums\OrderStatus::CANCELLED->value)->count(),
            'total_revenue' => Order::where('status', \App\Enums\OrderStatus::DELIVERED->value)->sum('total_amount'),
            'pending_revenue' => Order::whereIn('status', [
                \App\Enums\OrderStatus::PENDING->value,
                \App\Enums\OrderStatus::CONFIRMED->value,
                \App\Enums\OrderStatus::PROCESSING->value,
                \App\Enums\OrderStatus::SHIPPED->value,
            ])->sum('total_amount'),
            // مفاتيح إضافية للتوافق مع الكود القديم
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', \App\Enums\OrderStatus::PENDING->value)->count(),
            'confirmed_orders' => Order::where('status', \App\Enums\OrderStatus::CONFIRMED->value)->count(),
            'processing_orders' => Order::where('status', \App\Enums\OrderStatus::PROCESSING->value)->count(),
            'shipped_orders' => Order::where('status', \App\Enums\OrderStatus::SHIPPED->value)->count(),
            'delivered_orders' => Order::where('status', \App\Enums\OrderStatus::DELIVERED->value)->count(),
            'cancelled_orders' => Order::where('status', \App\Enums\OrderStatus::CANCELLED->value)->count(),
        ];
    }
}