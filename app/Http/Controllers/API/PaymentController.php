<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * إنشاء جلسة دفع Stripe لشحن المحفظة
     * POST /api/payment/create-checkout-session
     */
    public function createCheckoutSession(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();

        try {
            // تعيين مفتاح Stripe السري
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // إنشاء جلسة الدفع
            $session = \Stripe\Checkout\Session::create([
                'customer_email' => $user->email,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'شحن المحفظة',
                            'description' => 'شحن المحفظة الرقمية',
                        ],
                        'unit_amount' => $request->amount * 100, // المبلغ بالسنت
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $request->input('success_url', url('/api/payment/success')) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $request->input('cancel_url', url('/api/payment/cancel')),
                'metadata' => [
                    'user_id' => $user->id,
                    'amount' => $request->amount * 100,
                    'type' => 'wallet_recharge',
                ],
            ]);

            Log::info('Stripe checkout session created', [
                'user_id' => $user->id,
                'session_id' => $session->id,
                'amount' => $request->amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء جلسة الدفع بنجاح',
                'data' => [
                    'session_id' => $session->id,
                    'checkout_url' => $session->url,
                    'amount' => (float) $request->amount,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create Stripe checkout session', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء جلسة الدفع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * التحقق من حالة جلسة الدفع
     * GET /api/payment/check-session/{sessionId}
     */
    public function checkSession(string $sessionId): JsonResponse
    {
        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            return response()->json([
                'success' => true,
                'message' => 'تم جلب حالة الجلسة بنجاح',
                'data' => [
                    'session_id' => $session->id,
                    'payment_status' => $session->payment_status,
                    'status' => $session->status,
                    'amount_total' => $session->amount_total ? $session->amount_total / 100 : 0,
                    'currency' => $session->currency,
                    'customer_email' => $session->customer_email,
                ],
                'timestamp' => now()->toIso8601String(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل جلب حالة الجلسة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * صفحة النجاح (للتحقق من الدفع)
     * GET /api/payment/success
     */
    public function success(Request $request): JsonResponse
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'معرف الجلسة مطلوب',
            ], 400);
        }

        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            return response()->json([
                'success' => true,
                'message' => 'تم الدفع بنجاح',
                'data' => [
                    'session_id' => $session->id,
                    'payment_status' => $session->payment_status,
                    'amount_total' => $session->amount_total ? $session->amount_total / 100 : 0,
                    'currency' => $session->currency,
                ],
                'timestamp' => now()->toIso8601String(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من الدفع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * صفحة الإلغاء
     * GET /api/payment/cancel
     */
    public function cancel(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'تم إلغاء عملية الدفع',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * إنشاء جلسة دفع Stripe لطلب معين
     * POST /api/orders/{order}/pay
     */
    public function payOrder(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        // التحقق من أن المستخدم ليس أدمن
        if ($user instanceof \App\Models\Admin) {
            return response()->json([
                'success' => false,
                'message' => 'الأدمن لا يمكنه الدفع. الدفع متاح للمستخدمين فقط.',
            ], 403);
        }

        // التحقق من أن المستخدم يملك الطلب
        if ($order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للدفع على هذا الطلب',
            ], 403);
        }

        // التحقق من أن الطلب في حالة pending
        if ($order->status !== \App\Enums\OrderStatus::PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن الدفع على طلب غير قيد الانتظار',
            ], 422);
        }

        try {
            // تعيين مفتاح Stripe السري
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // إنشاء جلسة الدفع للطلب
            $session = \Stripe\Checkout\Session::create([
                'customer_email' => $user->email,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'طلب #' . $order->id,
                            'description' => 'دفع طلب من متجر Kandurah',
                        ],
                        'unit_amount' => (int)($order->total_amount * 100), // المبلغ بالسنت
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $request->input('success_url', url('/api/payment/order-success')) . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
                'cancel_url' => $request->input('cancel_url', url('/api/payment/order-cancel') . '?order_id=' . $order->id),
                'metadata' => [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => (int)($order->total_amount * 100),
                    'type' => 'order_payment',
                ]
            ]);

            Log::info('Stripe checkout session created for order', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'session_id' => $session->id,
                'amount' => $order->total_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء جلسة الدفع بنجاح',
                'data' => [
                    'session_id' => $session->id,
                    'checkout_url' => $session->url,
                    'order_id' => $order->id,
                    'amount' => (float) $order->total_amount,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create Stripe checkout session for order', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء جلسة الدفع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * صفحة نجاح دفع الطلب (Public - للعودة من Stripe)
     * GET /api/payment/order-success
     */
    public function orderSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        $orderId = $request->get('order_id');

        if (!$sessionId) {
            return view('payment.order-success', [
                'error' => 'معرف الجلسة مطلوب',
            ]);
        }

        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // التحقق من صحة الجلسة من Stripe
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            // التحقق من أن الدفع تم بنجاح
            if ($session->payment_status !== 'paid') {
                return view('payment.order-success', [
                    'error' => 'لم يتم إتمام الدفع بعد',
                    'session' => $session,
                ]);
            }

            // التحقق من أن order_id في metadata يطابق order_id في URL
            $metadataOrderId = $session->metadata->order_id ?? null;
            $finalOrderId = $orderId ?? $metadataOrderId;
            $order = null;
            $statusUpdated = false;

            if ($finalOrderId) {
                $order = Order::find($finalOrderId);
                if ($order) {
                    // تحديث حالة الطلب إلى PAID إذا كان في حالة PENDING
                    if ($order->status === \App\Enums\OrderStatus::PENDING) {
                        try {
                            DB::transaction(function () use ($order) {
                                $this->orderService->updateStatus($order, \App\Enums\OrderStatus::PAID);
                            });

                            $statusUpdated = true;

                            Log::info('Order status updated to PAID in orderSuccess callback', [
                                'order_id' => $order->id,
                                'session_id' => $sessionId,
                            ]);
                        } catch (\Exception $e) {
                            Log::warning('Failed to update order status in orderSuccess', [
                                'order_id' => $order->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    // إعادة تحميل الطلب للحصول على الحالة المحدثة
                    $order->refresh();
                }
            }

            return view('payment.order-success', [
                'session' => $session,
                'order' => $order,
                'statusUpdated' => $statusUpdated,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to verify payment in orderSuccess', [
                'session_id' => $sessionId,
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return view('payment.order-success', [
                'error' => 'فشل التحقق من الدفع: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * صفحة إلغاء دفع الطلب
     * GET /api/payment/order-cancel
     */
    public function orderCancel(Request $request): JsonResponse
    {
        $orderId = $request->get('order_id');

        $data = [
            'message' => 'تم إلغاء عملية الدفع',
        ];

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $data['order'] = [
                    'id' => $order->id,
                    'status' => $order->status->value,
                ];
            }
        }

        return response()->json([
            'success' => false,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

