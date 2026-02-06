<?php

namespace App\Services;

use App\Models\Measurement;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MeasurementService
{
    /**
     * الحصول على جميع مقاسات المستخدم
     */
    public function getUserMeasurements(int $userId, array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Measurement::where('user_id', $userId);

        // البحث (إذا كان هناك حقل للبحث)
        if (!empty($filters['search'])) {
            // يمكن إضافة منطق البحث هنا إذا لزم الأمر
        }

        // الترتيب
        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortColumn, $sortDirection);

        // إذا كان get_all = true، إرجاع جميع المقاسات بدون pagination
        if (isset($filters['get_all']) && $filters['get_all'] === true) {
            return $query->get();
        }

        // Pagination
        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * إنشاء قياس جديد
     */
    public function createMeasurement(int $userId, array $data): Measurement
    {
        $data['user_id'] = $userId;
        
        return Measurement::create($data);
    }

    /**
     * تحديث قياس موجود
     */
    public function updateMeasurement(Measurement $measurement, array $data): Measurement
    {
        $measurement->update($data);
        
        return $measurement->fresh();
    }

    /**
     * حذف قياس
     */
    public function deleteMeasurement(Measurement $measurement): bool
    {
        return $measurement->delete();
    }
}
