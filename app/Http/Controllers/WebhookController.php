<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\WalletRecharged;

class WebhookController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function handle(Request $request)
    {
        // الحصول على محتوى الطلب والتوقيع
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        try {
            // التحقق من صحة الـ Webhook (كما في الدليل)
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig,
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // معالجة الحدث
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                
                // الحصول على معلومات المستخدم من metadata
                $userId = $session->metadata->user_id ?? null;
                $amount = isset($session->metadata->amount) ? $session->metadata->amount / 100 : 0; // تحويل من سنت
                $paymentType = $session->metadata->type ?? 'wallet_recharge';
                $orderId = $session->metadata->order_id ?? null;

                // تحديد نوع الدفع
                if ($paymentType === 'order_payment' && $orderId) {
                    // معالجة دفع الطلب
                    $order = Order::find($orderId);
                    if ($order) {
                        // التحقق من أن الدفع تم بنجاح من Stripe
                        $paymentStatus = $session->payment_status ?? null;
                        
                        if ($paymentStatus === 'paid' && $order->status === \App\Enums\OrderStatus::PENDING) {
                            // استخدام DB transaction لضمان إدخال البيانات في كلا الجدولين
                            DB::transaction(function () use ($order, $userId, $amount, $session, $orderId) {
                                // 1. تحديث حالة الطلب إلى مدفوع في جدول orders
                                $this->orderService->updateStatus($order, \App\Enums\OrderStatus::PAID);

                                // 2. حفظ سجل المعاملة في جدول transactions
                                $transaction = Transaction::create([
                                    'user_id' => $userId,
                                    'amount' => $amount,
                                    'type' => 'purchase', // استخدام 'purchase' بدلاً من 'order_payment' للتوافق مع migration
                                    'status' => 'completed',
                                    'stripe_session_id' => $session->id,
                                    'payment_intent' => $session->payment_intent,
                                    'description' => 'دفع طلب #' . $order->id . ' عبر Stripe',
                                    'metadata' => [
                                        'order_id' => $orderId,
                                        'payment_type' => 'order_payment',
                                    ],
                                ]);

                                // التحقق من إنشاء المعاملة بنجاح
                                if (!$transaction || !$transaction->id) {
                                    throw new \Exception('فشل إنشاء المعاملة في جدول transactions');
                                }

                                Log::info("Order payment successful via webhook", [
                                    'order_id' => $orderId,
                                    'user_id' => $userId,
                                    'amount' => $amount,
                                    'transaction_id' => $transaction->id,
                                    'old_status' => 'pending',
                                    'new_status' => 'paid',
                                ]);
                            });
                        } else {
                            Log::warning("Order payment webhook received but order status not updated", [
                                'order_id' => $orderId,
                                'current_status' => $order->status->value,
                                'payment_status' => $paymentStatus,
                                'expected_status' => 'pending',
                            ]);
                        }
                    } else {
                        Log::error("Order not found in webhook", [
                            'order_id' => $orderId,
                        ]);
                    }
                } else {
                    // معالجة شحن المحفظة
                $user = User::find($userId);
                if ($user) {
                    // إضافة المبلغ للمحفظة
                    $user->wallet_balance += $amount;
                    $user->save();

                    // حفظ سجل المعاملة في جدول transactions
                    $transaction = Transaction::create([
                        'user_id' => $userId,
                        'amount' => $amount,
                        'type' => 'deposit',
                        'status' => 'completed',
                        'stripe_session_id' => $session->id,
                        'payment_intent' => $session->payment_intent,
                        'description' => 'شحن المحفظة عبر Stripe',
                        'metadata' => [
                            'payment_type' => 'wallet_recharge',
                        ],
                    ]);

                    // التحقق من إنشاء المعاملة بنجاح
                    if (!$transaction || !$transaction->id) {
                        Log::error("Failed to create transaction for wallet recharge", [
                            'user_id' => $userId,
                            'amount' => $amount,
                        ]);
                    }

                    // إرسال إشعار للمستخدم
                    try {
                        $user->notify(new WalletRecharged($amount, $transaction->id));
                    } catch (\Exception $e) {
                        Log::warning("Failed to send notification: " . $e->getMessage());
                    }

                        Log::info("Wallet recharge successful", [
                            'user_id' => $userId,
                            'amount' => $amount,
                        ]);
                    }
                }
                break;

            case 'payment_intent.succeeded':
                // معالجة نجاح الدفع
                Log::info('PaymentIntent succeeded');
                break;

            case 'payment_intent.payment_failed':
                // معالجة فشل الدفع
                Log::error('PaymentIntent failed');
                break;

            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'ok']);
    }
}