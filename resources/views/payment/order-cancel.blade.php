<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إلغاء الدفع - Payment Cancelled</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .cancel-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 15px 50px rgba(0,0,0,0.2);
            max-width: 600px;
        }
        .cancel-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .cancel-icon i {
            font-size: 50px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="cancel-card">
        <div class="cancel-icon">
            <i class="fas fa-times"></i>
        </div>

        <h1 class="text-danger mb-3">تم إلغاء عملية الدفع</h1>
        <p class="lead text-muted mb-4">لم يتم إتمام عملية الدفع. يمكنك المحاولة مرة أخرى.</p>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>ملاحظة:</strong> لم يتم خصم أي مبلغ من حسابك
        </div>

        <div class="mt-4">
            <a href="{{ route('dashboard.orders.show', $order) }}" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-right"></i>
                العودة للطلب
            </a>

            <a href="{{ route('dashboard.orders.index') }}" class="btn btn-outline-secondary btn-lg me-2">
                <i class="fas fa-list"></i>
                جميع الطلبات
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

