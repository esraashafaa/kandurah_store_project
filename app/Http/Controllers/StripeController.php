<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    // صفحة عرض نموذج الدفع
    public function showPaymentForm()
    {
        return view('payment.form');
    }

    // إنشاء جلسة الدفع
    public function checkout(Request $request)
    {
        // التحقق من البيانات
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $user = auth()->user();

        // تعيين مفتاح Stripe السري (كما في الدليل)
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // إنشاء جلسة الدفع
            $session = \Stripe\Checkout\Session::create([
                'customer_email' => $user->email,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'شحن المحفظة', // اسم المنتج
                        ],
                        'unit_amount' => $request->amount * 100, // المبلغ بالسنت
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),
                'metadata' => [
                    'user_id' => $user->id,
                    'amount' => $request->amount * 100
                ]
            ]);

            // إعادة توجيه المستخدم لصفحة الدفع
            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // صفحة النجاح
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if ($sessionId) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            
            try {
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                
                return view('payment.success', [
                    'session' => $session
                ]);
            } catch (\Exception $e) {
                return redirect()->route('payment.form')
                    ->with('error', 'فشل التحقق من الدفع');
            }
        }

        return view('payment.success');
    }

    // صفحة الإلغاء
    public function cancel()
    {
        return view('payment.cancel');
    }

    /**
     * دفع طلب معين
     * POST /orders/{order}/pay
     */
    public function payOrder(Request $request, Order $order)
    {
        // التحقق من أن المستخدم يملك الطلب
        if ($order->user_id !== auth()->id()) {
            abort(403, 'ليس لديك صلاحية للدفع على هذا الطلب');
        }

        // التحقق من أن الطلب في حالة pending
        if ($order->status !== \App\Enums\OrderStatus::PENDING) {
            return back()->with('error', 'لا يمكن الدفع على طلب غير قيد الانتظار');
        }

        $user = auth()->user();

        // تعيين مفتاح Stripe السري
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
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
                'success_url' => route('stripe.order.success', $order) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.order.cancel', $order),
                'metadata' => [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => (int)($order->total_amount * 100),
                    'type' => 'order_payment',
                ]
            ]);

            // إعادة توجيه المستخدم لصفحة الدفع
            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة نجاح دفع الطلب
     */
    public function orderSuccess(Request $request, Order $order)
    {
        $sessionId = $request->get('session_id');
        
        if ($sessionId) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            
            try {
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                
                return view('payment.order-success', [
                    'session' => $session,
                    'order' => $order
                ]);
            } catch (\Exception $e) {
                return redirect()->route('dashboard.orders.show', $order)
                    ->with('error', 'فشل التحقق من الدفع');
            }
        }

        return view('payment.order-success', ['order' => $order]);
    }

    /**
     * صفحة إلغاء دفع الطلب
     */
    public function orderCancel(Order $order)
    {
        return view('payment.order-cancel', ['order' => $order]);
    }
}