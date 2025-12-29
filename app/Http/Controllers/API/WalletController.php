<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    /**
     * عرض رصيد المحفظة
     * GET /api/wallet
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب بيانات المحفظة بنجاح',
            'data' => [
                'balance' => (float) $user->wallet_balance,
                'formatted_balance' => number_format($user->wallet_balance, 2) . ' ريال',
                'currency' => 'SAR',
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * عرض سجل المعاملات
     * GET /api/wallet/transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = $user->transactions()
            ->orderBy('created_at', 'desc');

        // فلترة حسب النوع
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // فلترة حسب التاريخ
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        $perPage = $request->input('per_page', 15);
        $transactions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب سجل المعاملات بنجاح',
            'data' => TransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * إضافة رصيد للمحفظة (Admin فقط)
     * POST /api/wallet/deposit
     */
    public function deposit(Request $request): JsonResponse
    {
        // التحقق من الصلاحيات
        if (!$request->user()->hasRole('admin') && !$request->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء',
            ], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->input('user_id'));
            $amount = $request->input('amount');
            $description = $request->input('description', 'إضافة رصيد من قبل الأدمن');

            // إضافة الرصيد
            $transaction = $user->addFunds($amount, $description);

            Log::info('Admin added funds to wallet', [
                'admin_id' => $request->user()->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الرصيد بنجاح',
                'data' => [
                    'transaction' => new TransactionResource($transaction),
                    'new_balance' => (float) $user->fresh()->wallet_balance,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to deposit funds', [
                'error' => $e->getMessage(),
                'user_id' => $request->input('user_id'),
                'amount' => $request->input('amount'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل إضافة الرصيد',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * سحب رصيد من المحفظة (Admin فقط)
     * POST /api/wallet/withdraw
     */
    public function withdraw(Request $request): JsonResponse
    {
        // التحقق من الصلاحيات
        if (!$request->user()->hasRole('admin') && !$request->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء',
            ], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->input('user_id'));
            $amount = $request->input('amount');
            $description = $request->input('description', 'سحب رصيد من قبل الأدمن');

            // التحقق من الرصيد الكافي
            if ($user->wallet_balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'الرصيد غير كافي',
                    'data' => [
                        'current_balance' => (float) $user->wallet_balance,
                        'requested_amount' => (float) $amount,
                    ],
                ], 422);
            }

            // سحب الرصيد
            $transaction = $user->deductFunds($amount, $description);

            Log::info('Admin withdrew funds from wallet', [
                'admin_id' => $request->user()->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم سحب الرصيد بنجاح',
                'data' => [
                    'transaction' => new TransactionResource($transaction),
                    'new_balance' => (float) $user->fresh()->wallet_balance,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to withdraw funds', [
                'error' => $e->getMessage(),
                'user_id' => $request->input('user_id'),
                'amount' => $request->input('amount'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل سحب الرصيد',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

