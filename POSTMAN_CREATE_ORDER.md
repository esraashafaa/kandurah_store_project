# 📦 إنشاء طلب جديد عبر Postman

## 🔗 Endpoint
```
POST http://localhost:8000/api/orders
```

## 🔐 Headers المطلوبة
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

## 📝 Body Structure (JSON)

### الطريقة الأولى: إنشاء طلب من items مباشرة

```json
{
  "location_id": 1,
  "notes": "ملاحظات اختيارية للطلب",
  "items": [
    {
      "design_id": 1,
      "size_id": 1,
      "quantity": 2,
      "design_option_ids": [1, 2, 3]
    },
    {
      "design_id": 2,
      "size_id": 2,
      "quantity": 1,
      "design_option_ids": [4]
    }
  ]
}
```

### الطريقة الثانية: إنشاء طلب من السلة (Cart)
```json
{
  "location_id": 1,
  "notes": "ملاحظات اختيارية"
}
```
> ملاحظة: يجب أن تكون السلة غير فارغة

## 📋 الحقول المطلوبة

### الحقول الأساسية:
- `location_id` (required, integer): معرف العنوان - يجب أن يكون يخص المستخدم
- `notes` (optional, string, max:1000): ملاحظات الطلب

### عند إرسال items مباشرة:
- `items` (required, array, min:1): مصفوفة العناصر
  - `items[].design_id` (required, integer): معرف التصميم
  - `items[].size_id` (required, integer): معرف المقاس
  - `items[].quantity` (required, integer, min:1): الكمية
  - `items[].design_option_ids` (optional, array): مصفوفة معرفات خيارات التصميم

## ✅ Response Success (201)
```json
{
  "success": true,
  "message": "تم إنشاء الطلب بنجاح",
  "data": {
    "id": 1,
    "user_id": 1,
    "location_id": 1,
    "total_amount": "150.00",
    "status": "pending",
    "notes": "ملاحظات اختيارية للطلب",
    "created_at": "2025-01-15T10:30:00.000000Z",
    "items": [
      {
        "id": 1,
        "design_id": 1,
        "size_id": 1,
        "quantity": 2,
        "price": "50.00",
        "subtotal": "100.00"
      }
    ],
    "location": {
      "id": 1,
      "city": "الرياض",
      "area": "العليا"
    }
  }
}
```

## ❌ Response Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "location_id": ["يجب اختيار عنوان الشحن"],
    "items.0.design_id": ["The selected items.0.design id is invalid."]
  }
}
```

## 📝 مثال كامل في Postman

### Step 1: تسجيل الدخول للحصول على Token
```
POST http://localhost:8000/api/login
Body:
{
  "email": "user@example.com",
  "password": "password"
}
```

### Step 2: إنشاء طلب جديد
```
POST http://localhost:8000/api/orders
Headers:
  Authorization: Bearer {token_from_step_1}
  Content-Type: application/json

Body (raw JSON):
{
  "location_id": 1,
  "notes": "يرجى التوصيل في الصباح",
  "items": [
    {
      "design_id": 1,
      "size_id": 1,
      "quantity": 2,
      "design_option_ids": [1, 2]
    }
  ]
}
```

## 🔍 ملاحظات مهمة

1. **location_id**: يجب أن يكون العنوان يخص المستخدم المسجل دخوله
2. **design_id**: يجب أن يكون التصميم موجوداً ونشطاً
3. **size_id**: يجب أن يكون المقاس موجوداً ونشطاً
4. **design_option_ids**: اختياري - يمكن إرسال مصفوفة فارغة أو حذف الحقل
5. **الطلب يتم إنشاؤه بحالة `pending`** - يمكن الدفع عليه لاحقاً

## 🧪 أمثلة للاختبار

### مثال 1: طلب بسيط (عنصر واحد)
```json
{
  "location_id": 1,
  "items": [
    {
      "design_id": 1,
      "size_id": 1,
      "quantity": 1
    }
  ]
}
```

### مثال 2: طلب متعدد العناصر
```json
{
  "location_id": 1,
  "notes": "طلب عاجل",
  "items": [
    {
      "design_id": 1,
      "size_id": 1,
      "quantity": 2,
      "design_option_ids": [1, 2, 3]
    },
    {
      "design_id": 2,
      "size_id": 2,
      "quantity": 1,
      "design_option_ids": [4]
    }
  ]
}
```

### مثال 3: طلب بدون خيارات تصميم
```json
{
  "location_id": 1,
  "items": [
    {
      "design_id": 1,
      "size_id": 1,
      "quantity": 1
    }
  ]
}
```


