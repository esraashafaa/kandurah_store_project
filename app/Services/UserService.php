<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * UserService - خدمة إدارة المستخدمين
 * هذا الكلاس مسؤول عن كل العمليات المتعلقة بالمستخدمين
 */
class UserService
{
    /**
     * الحصول على قائمة المستخدمين مع الفلترة والبحث
     * 
     * @param array $filters - مصفوفة الفلاتر (search, role, is_active, per_page)
     * @return LengthAwarePaginator - قائمة المستخدمين مع pagination
     */
    public function getAllUsers(array $filters = []): LengthAwarePaginator
    {
        // نبدأ استعلام قاعدة البيانات
        $query = User::query();

        // فلترة حسب البحث (الاسم أو البريد الإلكتروني)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            // نبحث في اسم المستخدم أو البريد الإلكتروني أو رقم الهاتف
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // فلترة حسب حالة النشاط (نشط أو غير نشط)
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // ترتيب النتائج من الأحدث للأقدم
        $query->latest();

        // عدد النتائج في كل صفحة (افتراضياً 15)
        $perPage = $filters['per_page'] ?? 15;

        // إرجاع النتائج مع pagination
        return $query->paginate($perPage);
    }

    /**
     * الحصول على مستخدم واحد بواسطة ID
     * 
     * @param int $userId - معرف المستخدم
     * @return User - بيانات المستخدم
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUserById(int $userId): User
    {
        // نبحث عن المستخدم بواسطة ID
        // findOrFail يرمي استثناء 404 إذا لم يجد المستخدم
        return User::findOrFail($userId);
    }

    /**
     * إنشاء مستخدم جديد
     * 
     * @param array $data - بيانات المستخدم (name, email, password, etc.)
     * @return User - المستخدم المنشأ
     */
    public function createUser(array $data): User
    {
        // نستخدم transaction لضمان إنشاء المستخدم والدور معاً أو لا شيء
        return DB::transaction(function () use ($data) {
            
            // التحقق من وجود كلمة المرور
            if (empty($data['password'])) {
                throw new \InvalidArgumentException('كلمة المرور مطلوبة');
            }

            // التعامل مع رفع الصورة إن وجدت
            if (isset($data['avatar'])) {
                // التحقق من أن الملف هو صورة
                if (!$data['avatar']->isValid()) {
                    throw new \InvalidArgumentException('ملف الصورة غير صالح');
                }
                
                // التحقق من نوع الملف (mime type)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($data['avatar']->getMimeType(), $allowedMimes)) {
                    throw new \InvalidArgumentException('نوع الملف غير مدعوم. يجب أن تكون الصورة من نوع: JPEG, PNG, GIF, أو WebP');
                }
                
                // حفظ الصورة في مجلد users وإرجاع المسار
                $data['avatar'] = $data['avatar']->store('users', 'public');
            }

            // ملاحظة: لا نحتاج لتشفير كلمة المرور يدوياً
            // لأن Laravel يقوم بذلك تلقائياً عند وجود 'hashed' في $casts

            // لا يوجد role للمستخدمين العاديين

            // تعيين حالة النشاط الافتراضية
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            // إنشاء المستخدم في قاعدة البيانات
            $user = User::create($data);

            // إرجاع المستخدم
            return $user;
        });
    }

    /**
     * تحديث بيانات مستخدم موجود
     * 
     * @param int $userId - معرف المستخدم
     * @param array $data - البيانات الجديدة
     * @return User - المستخدم المحدث
     */
    public function updateUser(User $user, array $data): User
    {
        // نستخدم transaction لضمان تحديث كل البيانات معاً
        return DB::transaction(function () use ($user, $data) {

            // التعامل مع رفع صورة جديدة
            if (isset($data['avatar'])) {
                // التحقق من أن الملف هو صورة
                if (!$data['avatar']->isValid()) {
                    throw new \InvalidArgumentException('ملف الصورة غير صالح');
                }
                
                // التحقق من نوع الملف (mime type)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($data['avatar']->getMimeType(), $allowedMimes)) {
                    throw new \InvalidArgumentException('نوع الملف غير مدعوم. يجب أن تكون الصورة من نوع: JPEG, PNG, GIF, أو WebP');
                }
                
                // حذف الصورة القديمة إن وجدت
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                // حفظ الصورة الجديدة
                $data['avatar'] = $data['avatar']->store('users', 'public');
            }

            // ملاحظة: لا نحتاج لتشفير كلمة المرور يدوياً
            // لأن Laravel يقوم بذلك تلقائياً عند وجود 'hashed' في $casts

            // تحديث بيانات المستخدم
            $user->update($data);

            // إرجاع المستخدم المحدث
            return $user->fresh();
        });
    }

    /**
     * حذف مستخدم (soft delete أو hard delete)
     * 
     * @param int $userId - معرف المستخدم
     * @return bool - true إذا نجح الحذف
     */
    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // حذف صورة المستخدم من التخزين
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // حذف جميع tokens المستخدم (تسجيلات الدخول)
            $user->tokens()->delete();

            // حذف المستخدم نفسه
            return $user->delete();
        });
    }

    /**
     * تفعيل أو تعطيل حساب مستخدم
     * 
     * @param int $userId - معرف المستخدم
     * @param bool $isActive - الحالة الجديدة (true = نشط, false = معطل)
     * @return User - المستخدم المحدث
     */
    public function toggleUserStatus(int $userId, bool $isActive): User
    {
        return DB::transaction(function () use ($userId, $isActive) {
            // البحث عن المستخدم
            $user = User::findOrFail($userId);

            // تحديث حالة النشاط
            $user->update(['is_active' => $isActive]);

            // إذا تم تعطيل المستخدم، نحذف جميع tokens الخاصة به
            if (!$isActive) {
                $user->tokens()->delete();
            }

            // إرجاع المستخدم المحدث
            return $user->fresh();
        });
    }

    /**
     * تغيير كلمة مرور المستخدم
     * 
     * @param int $userId - معرف المستخدم
     * @param string $newPassword - كلمة المرور الجديدة
     * @param bool $logoutFromAllDevices - هل نسجل خروج من جميع الأجهزة؟
     * @return User - المستخدم المحدث
     */
    public function changeUserPassword(int $userId, string $newPassword, bool $logoutFromAllDevices = false): User
    {
        return DB::transaction(function () use ($userId, $newPassword, $logoutFromAllDevices) {
            
            // البحث عن المستخدم
            $user = User::findOrFail($userId);

            // تحديث كلمة المرور
            // ملاحظة: لا نحتاج لتشفير كلمة المرور يدوياً
            // لأن Laravel يقوم بذلك تلقائياً عند وجود 'hashed' في $casts
            $user->update([
                'password' => $newPassword
            ]);

            // تسجيل الخروج من جميع الأجهزة إذا طلب ذلك
            if ($logoutFromAllDevices) {
                $user->tokens()->delete();
            }

            return $user;
        });
    }

    /**
     * البحث عن مستخدم بواسطة البريد الإلكتروني
     * 
     * @param string $email - البريد الإلكتروني
     * @return User|null - المستخدم أو null
     */
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * الحصول على إحصائيات المستخدمين
     * 
     * @return array - مصفوفة الإحصائيات
     */
    public function getUserStatistics(): array
    {
        return [
            // إجمالي عدد المستخدمين
            'total_users' => User::count(),
            
            // عدد المستخدمين النشطين
            'active_users' => User::where('is_active', true)->count(),
            
            // عدد المستخدمين غير النشطين
            'inactive_users' => User::where('is_active', false)->count(),
            
            // عدد المستخدمين المسجلين اليوم
            'users_today' => User::whereDate('created_at', today())->count(),
            
            // عدد المستخدمين المسجلين هذا الأسبوع
            'users_this_week' => User::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            
            // عدد المستخدمين المسجلين هذا الشهر
            'users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }
}



