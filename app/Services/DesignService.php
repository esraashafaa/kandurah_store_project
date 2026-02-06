<?php

namespace App\Services;

use App\Models\Design;
use App\Models\DesignImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DesignService
{
    /**
     * الحصول على جميع التصاميم مع الفلاتر والبحث (للأدمن)
     */
    public function getAllDesigns(array $filters = []): LengthAwarePaginator
    {
        $query = Design::with(['user:id,name,email', 'images', 'sizes', 'designOptions']);

        // البحث في اسم التصميم أو اسم المستخدم
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->search($search)
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // فلتر حسب المقاس
        if (!empty($filters['size_id'])) {
            $query->bySize($filters['size_id']);
        }

        // فلتر حسب نطاق السعر
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->priceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null);
        }

        // فلتر حسب خيار التصميم (Bonus)
        if (!empty($filters['design_option_id'])) {
            $query->byDesignOption($filters['design_option_id']);
        }

        // فلتر حسب المستخدم
        if (!empty($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        // فلتر التصاميم النشطة فقط
        if (isset($filters['is_active'])) {
            if ($filters['is_active']) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        // فلترة حسب التاريخ
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // الترتيب
        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->sortBy($sortColumn, $sortDirection);

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    /**
     * الحصول على تصاميم المستخدم الحالي
     */
    public function getMyDesigns(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Design::with(['images', 'sizes', 'designOptions'])
                       ->byUser($userId);

        // البحث
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // فلتر حسب المقاس
        if (!empty($filters['size_id'])) {
            $query->bySize($filters['size_id']);
        }

        // فلتر حسب نطاق السعر
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->priceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null);
        }

        // فلتر حسب خيار التصميم
        if (!empty($filters['design_option_id'])) {
            $query->byDesignOption($filters['design_option_id']);
        }

        // الترتيب
        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->sortBy($sortColumn, $sortDirection);

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    /**
     * الحصول على تصاميم الآخرين (Browse Others)
     */
    public function getOthersDesigns(int $currentUserId, array $filters = []): LengthAwarePaginator
    {
        $query = Design::with(['user:id,name,email,avatar', 'images', 'sizes', 'designOptions'])
                       ->excludeUser($currentUserId)
                       ->active(); // فقط التصاميم النشطة

        // البحث في الاسم والوصف
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // فلتر حسب المقاس
        if (!empty($filters['size_id'])) {
            $query->bySize($filters['size_id']);
        }

        // فلتر حسب نطاق السعر
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->priceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null);
        }

        // فلتر حسب خيار التصميم (Bonus)
        if (!empty($filters['design_option_id'])) {
            $query->byDesignOption($filters['design_option_id']);
        }

        // فلتر حسب المستخدم المنشئ (Bonus)
        if (!empty($filters['creator_id'])) {
            $query->byUser($filters['creator_id']);
        }

        // الترتيب
        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->sortBy($sortColumn, $sortDirection);

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    /**
     * الحصول على تصميم واحد بالتفاصيل
     */
    public function getDesignById(int $designId): Design
    {
        return Design::with([
            'user:id,name,email,avatar',
            'images',
            'sizes',
            'designOptions'
        ])->findOrFail($designId);
    }

    /**
     * إنشاء تصميم جديد
     */
    public function createDesign(int $userId, array $data): Design
    {
        return DB::transaction(function () use ($userId, $data) {
            // إنشاء التصميم
            $design = Design::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            // إضافة المقاسات
            if (!empty($data['size_ids'])) {
                $design->sizes()->attach($data['size_ids']);
            }

            // إضافة خيارات التصميم (اختياري)
            if (!empty($data['design_option_ids'])) {
                $design->designOptions()->attach($data['design_option_ids']);
            }

            // إضافة الصور
            if (!empty($data['images'])) {
                $this->handleImages($design, $data['images']);
            }

            $design->load(['images', 'sizes', 'designOptions']);

            // إطلاق حدث "تصميم جديد" مرة واحدة فقط من هنا (وليس من الـ Observer) لتفادي إرسال الإشعار مرتين
            \Illuminate\Support\Facades\Event::dispatch(new \App\Events\Designs\DesignCreated($design));

            return $design;
        });
    }

    /**
     * تحديث تصميم
     */
    public function updateDesign(Design $design, array $data): Design
    {
        return DB::transaction(function () use ($design, $data) {
            // تحديث البيانات الأساسية
            $design->update([
                'name' => $data['name'] ?? $design->name,
                'description' => $data['description'] ?? $design->description,
                'price' => $data['price'] ?? $design->price,
                'is_active' => $data['is_active'] ?? $design->is_active,
            ]);

            // تحديث المقاسات
            if (isset($data['size_ids'])) {
                $design->sizes()->sync($data['size_ids']);
            }

            // تحديث خيارات التصميم
            if (isset($data['design_option_ids'])) {
                $design->designOptions()->sync($data['design_option_ids']);
            }

            // تحديث الصور إذا تم إرسال صور جديدة
            if (!empty($data['images'])) {
                $this->handleImages($design, $data['images'], isset($data['keep_existing_images']) && $data['keep_existing_images']);
            }

            Log::info('Design updated', [
                'design_id' => $design->id,
                'user_id' => $design->user_id,
            ]);

            return $design->fresh(['images', 'sizes', 'designOptions']);
        });
    }

    /**
     * حذف تصميم
     */
    public function deleteDesign(Design $design): bool
    {
        return DB::transaction(function () use ($design) {
            $designId = $design->id;
            $userId = $design->user_id;

            // حذف جميع الصور
            foreach ($design->images as $image) {
                $path = $image->image_path;
                if (!empty($path) && is_string($path)) {
                    try {
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Could not delete design image file', [
                            'design_id' => $design->id,
                            'image_path' => $path,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
                $image->delete();
            }

            // حذف العلاقات
            $design->sizes()->detach();
            $design->designOptions()->detach();

            // حذف التصميم
            $deleted = $design->delete();

            if ($deleted) {
                Log::info('Design deleted', [
                    'design_id' => $designId,
                    'user_id' => $userId,
                ]);
            }

            return $deleted;
        });
    }

    /**
     * معالجة الصور (رفع وحفظ)
     */
    private function handleImages(Design $design, array $images, bool $keepExisting = false): void
    {
        // حذف الصور القديمة إذا لزم الأمر
        if (!$keepExisting) {
            foreach ($design->images as $oldImage) {
                if (Storage::disk('public')->exists($oldImage->image_path)) {
                    Storage::disk('public')->delete($oldImage->image_path);
                }
                $oldImage->delete();
            }
        }

        $sortOrder = $keepExisting ? $design->images()->max('sort_order') + 1 : 0;
        $isPrimarySet = $keepExisting ? $design->images()->where('is_primary', true)->exists() : false;

        foreach ($images as $index => $imageFile) {
            if ($imageFile && $imageFile->isValid()) {
                // حفظ الصورة في storage/app/public/designs
                $path = $imageFile->store('designs', 'public');

                DesignImage::create([
                    'design_id' => $design->id,
                    'image_path' => $path,
                    'sort_order' => $sortOrder + $index,
                    'is_primary' => !$isPrimarySet && $index === 0, // أول صورة تكون رئيسية
                ]);

                if (!$isPrimarySet && $index === 0) {
                    $isPrimarySet = true;
                }
            }
        }
    }

    /**
     * إحصائيات التصاميم
     */
    public function getDesignStats(): array
    {
        return [
            'total_designs' => Design::count(),
            'active_designs' => Design::active()->count(),
            'total_users_with_designs' => Design::distinct('user_id')->count('user_id'),
            'average_price' => Design::avg('price'),
            'designs_by_user' => Design::select('user_id', DB::raw('count(*) as count'))
                                       ->with('user:id,name')
                                       ->groupBy('user_id')
                                       ->orderBy('count', 'desc')
                                       ->limit(10)
                                       ->get(),
        ];
    }
}

