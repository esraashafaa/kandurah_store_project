<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * عرض إشعارات الأدمن الحالي فقط
     */
    public function index()
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            abort(403);
        }

        $notifiableType = get_class($admin);
        $notifiableId = $admin->id;

        $query = DB::table('notifications')
            ->where('notifiable_type', $notifiableType)
            ->where('notifiable_id', $notifiableId)
            ->orderBy('created_at', 'desc');

        if (request()->filled('search')) {
            $search = '%' . request('search') . '%';
            $query->where('data', 'like', $search);
        }
        if (request('status') === 'read') {
            $query->whereNotNull('read_at');
        }
        if (request('status') === 'unread') {
            $query->whereNull('read_at');
        }
        if (request()->filled('type')) {
            $query->where('data', 'like', '%"type":"' . request('type') . '"%');
        }

        $paginator = $query->paginate(20)->through(function ($row) {
            $data = json_decode($row->data, true) ?? [];
            return (object) [
                'id' => $row->id,
                'title' => $data['title'] ?? 'إشعار',
                'message' => $data['message'] ?? '',
                'type' => $data['type'] ?? 'system',
                'read_at' => $row->read_at,
                'created_at' => \Carbon\Carbon::parse($row->created_at),
                'user' => null,
            ];
        });

        $baseQuery = DB::table('notifications')
            ->where('notifiable_type', $notifiableType)
            ->where('notifiable_id', $notifiableId);
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'read' => (clone $baseQuery)->whereNotNull('read_at')->count(),
            'unread' => (clone $baseQuery)->whereNull('read_at')->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
        ];

        $usersForSelect = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);
        $isSuperAdmin = $admin->hasRole('super-admin')
            || (is_object($admin->role) && $admin->role->value === 'super_admin')
            || $admin->role === 'super_admin';

        return view('admin.notifications.index', [
            'notifications' => $paginator,
            'stats' => $stats,
            'usersForSelect' => $usersForSelect,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }

    /**
     * إرسال إشعار يدوي (للسوبر أدمن فقط)
     */
    public function send(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        $isSuperAdmin = $admin && ($admin->hasRole('super-admin')
            || (is_object($admin->role) && $admin->role->value === 'super_admin')
            || $admin->role === 'super_admin');
        if (!$isSuperAdmin) {
            return redirect()->route('admin.notifications.index')->with('error', 'غير مصرح.');
        }

        $request->validate([
            'recipient_type' => 'required|in:all,specific',
            'user_id' => 'required_if:recipient_type,specific|nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:system,order,promotion',
        ], [
            'user_id.required_if' => 'يجب اختيار مستخدم عند الإرسال لمستخدم محدد.',
            'user_id.exists' => 'المستخدم المحدد غير موجود. اختر مستخدماً من القائمة (جدول المستخدمين فقط، وليس الأدمن).',
        ]);

        $users = $request->recipient_type === 'all'
            ? User::where('is_active', true)->get()
            : collect([User::findOrFail($request->user_id)]);

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\GenericNotification(
                $request->title,
                $request->message,
                $request->type
            ));
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم إرسال الإشعار بنجاح');
    }

    /**
     * تعليم إشعار كمقروء (للأدمن الحالي فقط)
     */
    public function markAsRead(string $id)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return response()->json(['success' => false], 403);
        }
        $updated = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->update(['read_at' => now()]);
        return response()->json(['success' => (bool) $updated]);
    }

    /**
     * حذف إشعار (للأدمن الحالي فقط)
     */
    public function destroy(string $id)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return response()->json(['success' => false], 403);
        }
        $deleted = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->delete();
        return response()->json(['success' => (bool) $deleted]);
    }

    /**
     * إرسال إشعار تجريبي (للسوبر أدمن فقط)
     */
    public function sendTest(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        $isSuperAdmin = $admin && ($admin->hasRole('super-admin')
            || (is_object($admin->role) && $admin->role->value === 'super_admin')
            || $admin->role === 'super_admin');
        if (!$isSuperAdmin) {
            return redirect()->route('admin.notifications.index')->with('error', 'غير مصرح.');
        }

        $user = $request->user_id
            ? User::where('is_active', true)->find($request->user_id)
            : User::where('is_active', true)->first();

        if (!$user) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'لا يوجد مستخدم نشط لإرسال الإشعار التجريبي.');
        }

        $user->notify(new \App\Notifications\GenericNotification(
            'إشعار تجريبي',
            'هذا إشعار تجريبي من لوحة الإدارة. إذا وصلك فالإشعارات تعمل بشكل صحيح.',
            'system'
        ));

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم إرسال إشعار تجريبي إلى: ' . $user->name);
    }
}
