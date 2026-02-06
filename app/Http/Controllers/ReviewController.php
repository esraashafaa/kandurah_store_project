<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    /**
     * إنشاء تقييم جديد
     */
    public function store(Request $request, Order $order)
    {
        // التحقق من أن المستخدم يملك الطلب
        if ($order->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتقييم هذا الطلب');
        }

        // التحقق من أن الطلب مكتمل
        if (!$order->isCompleted()) {
            abort(400, 'يمكنك تقييم الطلبات المكتملة فقط');
        }

        // التحقق من عدم وجود تقييم مسبق
        if ($order->review) {
            abort(400, 'تم تقييم هذا الطلب مسبقاً');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'message' => 'تم إنشاء التقييم بنجاح',
            'review' => $review->load('user'),
        ], 201);
    }

    /**
     * تحديث التقييم
     */
    public function update(Request $request, Review $review)
    {
        // التحقق من أن المستخدم يملك التقييم
        if ($review->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا التقييم');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'message' => 'تم تحديث التقييم بنجاح',
            'review' => $review->load('user'),
        ]);
    }

    /**
     * حذف التقييم
     */
    public function destroy(Review $review)
    {
        // التحقق من أن المستخدم يملك التقييم أو هو أدمن
        if ($review->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بحذف هذا التقييم');
        }

        $review->delete();

        return response()->json([
            'message' => 'تم حذف التقييم بنجاح',
        ]);
    }

    /**
     * عرض التقييم
     */
    public function show(Review $review)
    {
        return response()->json([
            'review' => $review->load(['user', 'order']),
        ]);
    }

    /**
     * قائمة التقييمات للطلب
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'order']);

        // فلترة حسب المستخدم
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب الطلب
        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // فلترة حسب التقييم
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(15);

        return response()->json([
            'reviews' => $reviews,
        ]);
    }
}
