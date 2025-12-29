<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Design;
use App\Models\Coupon;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    /**
     * عرض لوحة التحكم الرئيسية
     */
    public function index()
    {
        // جمع الإحصائيات العامة
        $stats = $this->getStats();
        
        // أحدث الطلبات
        $recent_orders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();
        
        // أحدث التقييمات (قم بإضافة Model للتقييمات لاحقاً)
        $recent_reviews = [];
        
        return view('admin.dashboard', compact('stats', 'recent_orders', 'recent_reviews'));
    }

    /**
     * جمع الإحصائيات العامة
     */
    private function getStats(): array
    {
        return [
            // إحصائيات المستخدمين
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            
            // إحصائيات الطلبات
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', OrderStatus::PENDING)->count(),
            'processing_orders' => Order::where('status', OrderStatus::PROCESSING)->count(),
            'completed_orders' => Order::where('status', OrderStatus::DELIVERED)->count(),
            'cancelled_orders' => Order::where('status', OrderStatus::CANCELLED)->count(),
            
            // إحصائيات المبيعات
            'total_revenue' => Order::whereIn('status', [OrderStatus::DELIVERED, OrderStatus::SHIPPED])
                ->sum('total_amount') ?? 0,
            'revenue_today' => Order::whereIn('status', [OrderStatus::DELIVERED, OrderStatus::SHIPPED])
                ->whereDate('created_at', today())
                ->sum('total_amount') ?? 0,
            
            // إحصائيات التصاميم
            'total_designs' => Design::count(),
            'new_designs_today' => Design::whereDate('created_at', today())->count(),
            
            // إحصائيات الكوبونات
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::active()->valid()->count(),
            'expired_coupons' => Coupon::where(function ($q) {
                $q->whereNotNull('expires_at')
                  ->whereDate('expires_at', '<', now());
            })->count(),
            'used_coupons' => Coupon::where('usage_count', '>', 0)->count(),
            
            // نسب مئوية للرسوم البيانية
            'pending_percentage' => $this->calculatePercentage(
                Order::where('status', OrderStatus::PENDING)->count(),
                Order::count()
            ),
            'processing_percentage' => $this->calculatePercentage(
                Order::where('status', OrderStatus::PROCESSING)->count(),
                Order::count()
            ),
            'completed_percentage' => $this->calculatePercentage(
                Order::where('status', OrderStatus::DELIVERED)->count(),
                Order::count()
            ),
            'cancelled_percentage' => $this->calculatePercentage(
                Order::where('status', OrderStatus::CANCELLED)->count(),
                Order::count()
            ),
            
            // الإيرادات الشهرية (آخر 6 أشهر)
            'monthly_revenue' => $this->getMonthlyRevenue(),
        ];
    }

    /**
     * حساب النسبة المئوية
     */
    private function calculatePercentage(int $part, int $total): int
    {
        return $total > 0 ? round(($part / $total) * 100) : 0;
    }

    /**
     * جلب الإيرادات الشهرية لآخر 6 أشهر
     */
    private function getMonthlyRevenue(): array
    {
        $months = [];
        $maxRevenue = 0;
        
        // جمع البيانات لآخر 6 أشهر
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $amount = Order::whereIn('status', [OrderStatus::DELIVERED, OrderStatus::SHIPPED])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount') ?? 0;
            
            if ($amount > $maxRevenue) {
                $maxRevenue = $amount;
            }
            
            $months[] = [
                'name' => $date->locale('ar')->translatedFormat('F'),
                'amount' => $amount,
                'percentage' => 0,
            ];
        }
        
        // حساب النسب المئوية بناءً على أعلى قيمة
        foreach ($months as &$month) {
            $month['percentage'] = $maxRevenue > 0 
                ? round(($month['amount'] / $maxRevenue) * 100) 
                : 0;
        }
        
        return $months;
    }

    /**
     * عرض إحصائيات سريعة (لـ AJAX)
     */
    public function quickStats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', OrderStatus::PENDING)->count(),
            'revenue_today' => Order::whereIn('status', [OrderStatus::DELIVERED, OrderStatus::SHIPPED])
                ->whereDate('created_at', today())
                ->sum('total_amount') ?? 0,
        ]);
    }
}

