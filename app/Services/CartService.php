<?php

namespace App\Services;

use App\Models\Design;
use Illuminate\Support\Facades\Session;

/**
 * CartService
 * إدارة سلة التسوق باستخدام Session
 */
class CartService
{
    private const CART_SESSION_KEY = 'shopping_cart';
    
  
    public function addItem(int $designId, array $selectedOptions, int $quantity = 1): bool
    {
        $cart = $this->getCart();
        
        // إنشاء مفتاح فريد للعنصر (نفس التصميم بخيارات مختلفة = عنصر مختلف)
        $cartKey = $this->generateCartKey($designId, $selectedOptions);
        
        // إذا موجود، نزيد الكمية
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            // إضافة عنصر جديد
            $cart[$cartKey] = [
                'design_id' => $designId,
                'selected_options' => $selectedOptions,
                'quantity' => $quantity,
                'added_at' => now()->toDateTimeString()
            ];
        }
        
        Session::put(self::CART_SESSION_KEY, $cart);
        
        return true;
    }
    
    /**
     * تحديث كمية عنصر
     */
    public function updateQuantity(string $cartKey, int $quantity): bool
    {
        $cart = $this->getCart();
        
        if (!isset($cart[$cartKey])) {
            return false;
        }
        
        if ($quantity <= 0) {
            unset($cart[$cartKey]);
        } else {
            $cart[$cartKey]['quantity'] = $quantity;
        }
        
        Session::put(self::CART_SESSION_KEY, $cart);
        
        return true;
    }
    
    /**
     * حذف عنصر من السلة
     */
    public function removeItem(string $cartKey): bool
    {
        $cart = $this->getCart();
        
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            Session::put(self::CART_SESSION_KEY, $cart);
            return true;
        }
        
        return false;
    }
    
    /**
     * الحصول على جميع عناصر السلة مع تفاصيلها
     */
    public function getItems(): array
    {
        $cart = $this->getCart();
        $items = [];
        
        foreach ($cart as $key => $item) {
            $design = Design::with(['images', 'user'])
                ->find($item['design_id']);
            
            if ($design) {
                $items[$key] = [
                    'cart_key' => $key,
                    'design' => $design,
                    'selected_options' => $item['selected_options'],
                    'quantity' => $item['quantity'],
                    'price' => $design->price,
                    'subtotal' => $design->price * $item['quantity'],
                    'added_at' => $item['added_at']
                ];
            }
        }
        
        return $items;
    }
    
    /**
     * حساب المجموع الكلي
     */
    public function getTotal(): float
    {
        $items = $this->getItems();
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['subtotal'];
        }
        
        return $total;
    }
    
    /**
     * عدد العناصر في السلة
     */
    public function getCount(): int
    {
        return count($this->getCart());
    }
    
    /**
     * تفريغ السلة بالكامل
     */
    public function clear(): void
    {
        Session::forget(self::CART_SESSION_KEY);
    }
    
    /**
     * الحصول على السلة من الـ Session
     */
    private function getCart(): array
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }
    
    /**
     * توليد مفتاح فريد للعنصر
     */
    private function generateCartKey(int $designId, array $selectedOptions): string
    {
        // ترتيب الخيارات alphabetically
        ksort($selectedOptions);
        
        // إذا فيه arrays داخل الخيارات، نرتبهم كمان
        array_walk_recursive($selectedOptions, function(&$value) {
            if (is_array($value)) {
                sort($value);
            }
        });
        
        return 'design_' . $designId . '_' . md5(json_encode($selectedOptions));
    }
}