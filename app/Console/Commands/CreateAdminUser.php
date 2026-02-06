<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Enums\RoleEnum;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {--name= : اسم المستخدم}
                            {--email= : البريد الإلكتروني}
                            {--phone= : رقم الهاتف}
                            {--password= : كلمة المرور}
                            {--role=admin : الدور (admin أو super_admin)}
                            {--interactive : الوضع التفاعلي}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء مستخدم إداري جديد';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('═══════════════════════════════════════');
        $this->info('   إنشاء مستخدم إداري جديد');
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        // جمع البيانات
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');
        $role = $this->option('role');
        $interactive = $this->option('interactive');

        // إذا كان الوضع تفاعلي أو لم يتم توفير البيانات
        if ($interactive || empty($name) || empty($email) || empty($password)) {
            $name = $name ?: $this->ask('اسم المشرف الكامل');
            $email = $email ?: $this->ask('البريد الإلكتروني');
            $password = $password ?: $this->secret('كلمة المرور (8 أحرف على الأقل)');
            $passwordConfirmation = $this->secret('تأكيد كلمة المرور');
            
            if ($password !== $passwordConfirmation) {
                $this->error('❌ كلمات المرور غير متطابقة!');
                return 1;
            }
            
            $role = $this->choice('اختر الدور', ['admin' => 'مشرف', 'super_admin' => 'مشرف عام'], 'admin');
        }

        // التحقق من صحة البيانات
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,super_admin',
        ]);

        if ($validator->fails()) {
            $this->error('❌ خطأ في البيانات:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - ' . $error);
            }
            return 1;
        }

        // التحقق من وجود الدور في Spatie
        $spatieRoleName = $role === 'super_admin' ? 'super-admin' : 'admin';
        $spatieRole = Role::where('name', $spatieRoleName)
            ->where('guard_name', 'web')
            ->first();

        if (!$spatieRole) {
            $this->error("❌ لم يتم العثور على الدور '{$spatieRoleName}' في Spatie Permission");
            $this->warn('💡 قم بتشغيل: php artisan db:seed --class=RolesAndPermissionsSeeder');
            return 1;
        }

        // إنشاء المشرف
        try {
            $admin = Admin::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role === 'super_admin' ? RoleEnum::SUPER_ADMIN : RoleEnum::ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // تعيين الدور
            $admin->assignRole($spatieRole);

            $this->newLine();
            $this->info('✅ تم إنشاء المشرف بنجاح!');
            $this->newLine();
            $this->table(
                ['المعلومة', 'القيمة'],
                [
                    ['الاسم', $admin->name],
                    ['البريد الإلكتروني', $admin->email],
                    ['الدور', $role === 'super_admin' ? 'مشرف عام' : 'مشرف'],
                    ['حالة الحساب', $admin->is_active ? 'نشط ✓' : 'غير نشط ✗'],
                    ['البريد مؤكد', $admin->email_verified_at ? 'نعم ✓' : 'لا ✗'],
                ]
            );
            $this->newLine();
            $this->warn('⚠️  احفظ كلمة المرور في مكان آمن: ' . $password);
            $this->newLine();

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ حدث خطأ أثناء إنشاء المشرف:');
            $this->error('  ' . $e->getMessage());
            return 1;
        }
    }
}
