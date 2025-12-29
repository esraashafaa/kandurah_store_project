<?php

namespace App\Services;

use App\Enums\DesignOptionTypeEnum;
use App\Models\DesignOption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class DesignOptionService
{
    /**
     * الحصول على جميع خيارات التصميم
     */
    public function getAllOptions(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = DesignOption::query();

        // فلتر حسب النوع
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // فلتر الخيارات النشطة فقط
        if (isset($filters['is_active'])) {
            if ($filters['is_active']) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        // البحث في الاسم
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"]);
            });
        }

        // ترتيب
        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortColumn, $sortDirection);

        // إرجاع مع أو بدون pagination
        if (isset($filters['per_page']) && $filters['per_page'] > 0) {
            return $query->paginate($filters['per_page']);
        }

        return $query->get();
    }

    /**
     * الحصول على خيارات التصميم مجمعة حسب النوع
     */
    public function getOptionsGroupedByType(bool $activeOnly = true): array
    {
        $query = DesignOption::query();

        if ($activeOnly) {
            $query->active();
        }

        $options = $query->get();

        $grouped = [];
        foreach (DesignOptionTypeEnum::cases() as $type) {
            $grouped[$type->value] = [
                'type' => $type->value,
                'label' => $type->label(),
                'label_ar' => $type->labelAr(),
                'options' => $options->filter(fn($opt) => $opt->type === $type)->values(),
            ];
        }

        return $grouped;
    }

    /**
     * الحصول على خيار تصميم واحد
     */
    public function getOptionById(int $id): DesignOption
    {
        return DesignOption::findOrFail($id);
    }

    /**
     * إنشاء خيار تصميم جديد (للأدمن فقط)
     */
    public function createOption(array $data): DesignOption
    {
        $option = DesignOption::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        Log::info('Design option created', [
            'option_id' => $option->id,
            'type' => $option->type->value,
        ]);

        return $option;
    }

    /**
     * تحديث خيار تصميم (للأدمن فقط)
     */
    public function updateOption(DesignOption $option, array $data): DesignOption
    {
        $option->update([
            'name' => $data['name'] ?? $option->name,
            'type' => $data['type'] ?? $option->type,
            'is_active' => $data['is_active'] ?? $option->is_active,
        ]);

        Log::info('Design option updated', [
            'option_id' => $option->id,
        ]);

        return $option->fresh();
    }

    /**
     * حذف خيار تصميم (للأدمن فقط)
     */
    public function deleteOption(DesignOption $option): bool
    {
        $optionId = $option->id;

        // فصل العلاقات مع التصاميم
        $option->designs()->detach();

        $deleted = $option->delete();

        if ($deleted) {
            Log::info('Design option deleted', [
                'option_id' => $optionId,
            ]);
        }

        return $deleted;
    }

    /**
     * الحصول على أنواع الخيارات المتاحة
     */
    public function getOptionTypes(): array
    {
        return DesignOptionTypeEnum::options();
    }

    /**
     * إحصائيات خيارات التصميم
     */
    public function getOptionStats(): array
    {
        $stats = [
            'total' => DesignOption::count(),
            'active' => DesignOption::active()->count(),
            'by_type' => [],
        ];

        foreach (DesignOptionTypeEnum::cases() as $type) {
            $stats['by_type'][$type->value] = [
                'label' => $type->label(),
                'count' => DesignOption::byType($type)->count(),
                'active_count' => DesignOption::byType($type)->active()->count(),
            ];
        }

        return $stats;
    }
}

