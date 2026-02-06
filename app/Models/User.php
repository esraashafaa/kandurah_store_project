<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The default guard name for Spatie Permissions
     * This is used when checking roles/permissions without specifying a guard
     * Spatie will automatically detect the guard from the auth context
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'wallet_balance',
        'fcm_token',
        'user_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
        'wallet_balance' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

public function locations()
{
    return $this->hasMany(Location::class);
}


public function defaultLocation()
{
    return $this->hasOne(Location::class)->where('is_default', true);
}

/**
 * العلاقة مع الطلبات
 */
public function orders()
{
    return $this->hasMany(Order::class);
}

/**
 * العلاقة مع التصاميم
 */
public function designs()
{
    return $this->hasMany(Design::class);
}

/**
 * Get user's transactions
 */
public function transactions()
{
    return $this->hasMany(Transaction::class);
}

/**
 * العلاقة مع الكوبونات المستخدمة
 */
public function usedCoupons()
{
    return $this->belongsToMany(Coupon::class, 'coupon_user')
                ->withPivot('order_id', 'used_at')
                ->withTimestamps()
                ->using(\App\Models\CouponUser::class);
}

/**
 * العلاقة مع التقييمات
 */
public function reviews()
{
    return $this->hasMany(Review::class);
}

/**
 * العلاقة مع المقاسات
 */
public function measurements()
{
    return $this->hasMany(Measurement::class);
}

/**
 * Add funds to user's wallet
 */
public function addFunds(float $amount, ?string $description = null): Transaction
{
    $this->increment('wallet_balance', $amount);
    
    $transaction = $this->transactions()->create([
        'amount' => $amount,
        'type' => 'deposit',
        'status' => 'completed',
        'description' => $description ?? 'Wallet recharge',
    ]);

    // تفعيل حدث شحن المحفظة
    event(new \App\Events\Wallet\WalletRecharged($this, $amount, $transaction));
    
    return $transaction;
}

    /**
     * Deduct funds from user's wallet
     */
    public function deductFunds(float $amount, ?string $description = null): Transaction
    {
        if ($this->wallet_balance < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }
        
        $this->decrement('wallet_balance', $amount);
        
        $transaction = $this->transactions()->create([
            'amount' => $amount,
            'type' => 'withdrawal',
            'status' => 'completed',
            'description' => $description ?? 'Wallet withdrawal',
        ]);

        // تفعيل حدث خصم من المحفظة
        event(new \App\Events\Wallet\WalletDeducted($this, $amount, $transaction, $description));
        
        return $transaction;
    }


    /**
     * إنشاء رقم مستخدم فريد - 10 أرقام عشوائية
     */
    public static function generateUserNumber(): string
    {
        do {
            $userNumber = str_pad(random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
            $exists = self::where('user_number', $userNumber)->exists();
        } while ($exists);

        return $userNumber;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->user_number)) {
                $user->user_number = self::generateUserNumber();
            }
        });
    }
}
