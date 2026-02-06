<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * عرض إشعارات المستخدم الحالي فقط
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $notifications = $user->notifications()->paginate(20);

        return view('user.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * تعليم إشعار كمقروء (للمستخدم الحالي فقط)
     */
    public function markAsRead(string $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false], 403);
        }
        $updated = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->update(['read_at' => now()]);
        return response()->json(['success' => (bool) $updated]);
    }

    /**
     * حذف إشعار (للمستخدم الحالي فقط)
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false], 403);
        }
        $deleted = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->delete();
        return response()->json(['success' => (bool) $deleted]);
    }
}
