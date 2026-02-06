<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class FixSuperAdminRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:fix-super-admin-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إصلاح تعيين دور super-admin للمستخدمين الذين لديهم role = super_admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('═══════════════════════════════════════');
        $this->info('   إصلاح تعيين دور super-admin');
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        // التحقق من وجود الدور في Spatie
        $superAdminRole = Role::where('name', 'super-admin')
            ->where('guard_name', 'web')
            ->first();

        if (!$superAdminRole) {
            $this->error('❌ لم يتم العثور على الدور super-admin في Spatie Permission');
            $this->warn('💡 قم بتشغيل: php artisan db:seed --class=RolesAndPermissionsSeeder');
            return 1;
        }

        // البحث عن جميع المشرفين الذين لديهم role = super_admin
        $admins = Admin::where('role', 'super_admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('⚠️  لم يتم العثور على أي مشرفين لديهم role = super_admin');
            return 0;
        }

        $this->info("📋 تم العثور على {$admins->count()} مشرف:");
        $this->newLine();

        $fixed = 0;
        foreach ($admins as $admin) {
            if (!$admin->hasRole($superAdminRole)) {
                $admin->assignRole($superAdminRole);
                $this->info("✅ تم تعيين دور super-admin للمشرف: {$admin->name} ({$admin->email})");
                $fixed++;
            } else {
                $this->line("ℹ️  المشرف {$admin->name} ({$admin->email}) لديه بالفعل دور super-admin");
            }
        }

        $this->newLine();
        if ($fixed > 0) {
            $this->info("✅ تم إصلاح {$fixed} مشرف بنجاح!");
        } else {
            $this->info("✅ جميع المشرفين لديهم الدور الصحيح بالفعل!");
        }

        return 0;
    }
}
