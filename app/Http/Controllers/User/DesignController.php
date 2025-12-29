<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignRequest;
use App\Http\Requests\UpdateDesignRequest;
use App\Models\Design;
use App\Models\Size;
use App\Models\DesignOption;
use App\Services\DesignService;
use App\Services\DesignOptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DesignController extends Controller
{
    protected DesignService $designService;
    protected DesignOptionService $designOptionService;

    public function __construct(
        DesignService $designService,
        DesignOptionService $designOptionService
    ) {
        $this->designService = $designService;
        $this->designOptionService = $designOptionService;
    }

    /**
     * عرض قائمة تصاميم المستخدم
     * GET /my-designs
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        $filters = $request->only([
            'search', 'size_id', 'min_price', 'max_price',
            'design_option_id', 'sort_by', 'sort_direction'
        ]);

        $designs = $this->designService->getMyDesigns($user->id, $filters);
        
        // للحصول على البيانات للفلاتر
        $sizes = Size::active()->ordered()->get();
        $designOptions = $this->designOptionService->getOptionsGroupedByType(true);

        return view('user.designs.index', compact('designs', 'sizes', 'designOptions'));
    }

    /**
     * عرض نموذج إنشاء تصميم جديد
     * GET /my-designs/create
     */
    public function create(): View
    {
        $this->authorize('create', Design::class);

        $sizes = Size::active()->ordered()->get();
        $designOptions = $this->designOptionService->getOptionsGroupedByType(true);

        return view('user.designs.create', compact('sizes', 'designOptions'));
    }

    /**
     * حفظ تصميم جديد
     * POST /my-designs
     */
    public function store(StoreDesignRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        $this->authorize('create', Design::class);

        $data = $request->validated();
        $data['images'] = $request->file('images', []);

        $design = $this->designService->createDesign($user->id, $data);

        return redirect()
            ->route('my-designs.show', $design)
            ->with('success', 'تم إنشاء التصميم بنجاح');
    }

    /**
     * عرض تفاصيل تصميم
     * GET /my-designs/{design}
     */
    public function show(Design $design): View
    {
        $this->authorize('view', $design);

        $design = $this->designService->getDesignById($design->id);

        return view('user.designs.show', compact('design'));
    }

    /**
     * عرض نموذج تعديل تصميم
     * GET /my-designs/{design}/edit
     */
    public function edit(Design $design): View
    {
        $this->authorize('update', $design);

        $design = $this->designService->getDesignById($design->id);
        $sizes = Size::active()->ordered()->get();
        $designOptions = $this->designOptionService->getOptionsGroupedByType(true);

        return view('user.designs.edit', compact('design', 'sizes', 'designOptions'));
    }

    /**
     * تحديث تصميم
     * PUT /my-designs/{design}
     */
    public function update(UpdateDesignRequest $request, Design $design): RedirectResponse
    {
        $this->authorize('update', $design);

        $data = $request->validated();
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        $design = $this->designService->updateDesign($design, $data);

        return redirect()
            ->route('my-designs.show', $design)
            ->with('success', 'تم تحديث التصميم بنجاح');
    }

    /**
     * حذف تصميم
     * DELETE /my-designs/{design}
     */
    public function destroy(Design $design): RedirectResponse
    {
        $this->authorize('delete', $design);

        $this->designService->deleteDesign($design);

        return redirect()
            ->route('my-designs.index')
            ->with('success', 'تم حذف التصميم بنجاح');
    }

    /**
     * تصفح تصاميم الآخرين
     * GET /designs/browse
     */
    public function browse(Request $request): View
    {
        $user = $request->user();
        
        $filters = $request->only([
            'search', 'size_id', 'min_price', 'max_price',
            'design_option_id', 'creator_id', 'sort_by', 'sort_direction'
        ]);

        $designs = $this->designService->getOthersDesigns($user->id, $filters);
        
        // للحصول على البيانات للفلاتر
        $sizes = Size::active()->ordered()->get();
        $designOptions = $this->designOptionService->getOptionsGroupedByType(true);
        $creators = \App\Models\User::whereHas('designs', function($q) {
            $q->where('is_active', true);
        })->select('id', 'name')->get();

        return view('user.designs.browse', compact('designs', 'sizes', 'designOptions', 'creators'));
    }
}

