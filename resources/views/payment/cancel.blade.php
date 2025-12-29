<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عملية ملغية - Payment Cancelled</title>
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
            max-width: 500px;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            animation: scaleIn 0.5s ease 0.2s both;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .cancel-icon i {
            font-size: 50px;
            color: white;
            animation: shake 0.5s ease 0.5s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .btn-retry {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }
        .btn-retry:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .reasons-list {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            text-align: right;
        }
        .reasons-list li {
            padding: 8px 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="cancel-card">
        <div class="cancel-icon">
            <i class="fas fa-times"></i>
        </div>

        <h1 class="text-danger mb-3">تم إلغاء العملية</h1>
        <p class="lead text-muted mb-4">لم يتم إكمال عملية الدفع</p>

        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            لم يتم خصم أي مبلغ من حسابك
        </div>

        <div class="reasons-list">
            <h6 class="text-center mb-3">
                <i class="fas fa-question-circle text-primary"></i>
                أسباب محتملة للإلغاء:
            </h6>
            <ul class="list-unstyled mb-0">
                <li>
                    <i class="fas fa-chevron-left text-muted small"></i>
                    قمت بالضغط على زر الرجوع أو الإلغاء
                </li>
                <li>
                    <i class="fas fa-chevron-left text-muted small"></i>
                    أغلقت صفحة الدفع قبل إكمال العملية
                </li>
                <li>
                    <i class="fas fa-chevron-left text-muted small"></i>
                    انتهت مهلة الجلسة (Session Timeout)
                </li>
                <li>
                    <i class="fas fa-chevron-left text-muted small"></i>
                    حدثت مشكلة تقنية
                </li>
            </ul>
        </div>

        <div class="mt-4">
            <h6 class="mb-3">هل تريد المحاولة مرة أخرى؟</h6>
            
            <a href="{{ route('payment.form') }}" class="btn btn-retry btn-lg">
                <i class="fas fa-redo"></i>
                إعادة المحاولة
            </a>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg me-2">
                <i class="fas fa-home"></i>
                العودة للرئيسية
            </a>
        </div>

        <div class="mt-4 pt-3 border-top">
            <small class="text-muted">
                <i class="fas fa-headset"></i>
                هل تواجه مشكلة؟ 
                <a href="#" class="text-decoration-none">تواصل مع الدعم</a>
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

