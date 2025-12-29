<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عملية ناجحة - Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 15px 50px rgba(0,0,0,0.2);
            max-width: 600px;
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
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
        .success-icon i {
            font-size: 50px;
            color: white;
        }
        .checkmark {
            animation: checkmark 0.8s ease 0.5s both;
        }
        @keyframes checkmark {
            0% { transform: scale(0) rotate(0deg); }
            50% { transform: scale(1.2) rotate(180deg); }
            100% { transform: scale(1) rotate(360deg); }
        }
        .amount-display {
            font-size: 48px;
            font-weight: bold;
            color: #11998e;
            margin: 20px 0;
        }
        .session-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: right;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .btn-dashboard {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            padding: 12px 40px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }
        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check checkmark"></i>
        </div>

        <h1 class="text-success mb-3">تمت العملية بنجاح!</h1>
        <p class="lead text-muted mb-4">تم شحن محفظتك بنجاح</p>

        @if(isset($session))
            <div class="amount-display">
                ${{ number_format($session->amount_total / 100, 2) }}
            </div>

            <div class="session-details">
                <h5 class="text-center mb-3">
                    <i class="fas fa-receipt text-primary"></i>
                    تفاصيل العملية
                </h5>
                
                <div class="detail-row">
                    <span class="text-muted">رقم الجلسة:</span>
                    <strong class="text-break small">{{ substr($session->id, 0, 20) }}...</strong>
                </div>

                <div class="detail-row">
                    <span class="text-muted">البريد الإلكتروني:</span>
                    <strong>{{ $session->customer_email }}</strong>
                </div>

                <div class="detail-row">
                    <span class="text-muted">المبلغ المدفوع:</span>
                    <strong class="text-success">${{ number_format($session->amount_total / 100, 2) }}</strong>
                </div>

                <div class="detail-row">
                    <span class="text-muted">حالة الدفع:</span>
                    <strong class="text-success">
                        <i class="fas fa-check-circle"></i>
                        مدفوع
                    </strong>
                </div>

                @if(isset($session->payment_intent))
                <div class="detail-row">
                    <span class="text-muted">رقم الدفع:</span>
                    <strong class="text-break small">{{ substr($session->payment_intent, 0, 20) }}...</strong>
                </div>
                @endif
            </div>
        @else
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                تم استلام الدفع وجاري معالجته
            </div>
        @endif

        <div class="mt-4">
            <p class="text-muted mb-3">
                <i class="fas fa-info-circle"></i>
                سيتم تحديث رصيدك خلال ثوانٍ قليلة
            </p>
            
            <a href="{{ route('dashboard') }}" class="btn btn-dashboard btn-lg">
                <i class="fas fa-home"></i>
                العودة للوحة التحكم
            </a>

            <a href="{{ route('payment.form') }}" class="btn btn-outline-secondary btn-lg me-2">
                <i class="fas fa-plus"></i>
                شحن مرة أخرى
            </a>
        </div>

        <div class="mt-4 pt-3 border-top">
            <small class="text-muted">
                <i class="fas fa-lock"></i>
                تم التشفير والحماية بواسطة Stripe
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confetti effect (optional)
        function createConfetti() {
            const colors = ['#11998e', '#38ef7d', '#FFD700', '#FF69B4'];
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.position = 'fixed';
                    confetti.style.width = '10px';
                    confetti.style.height = '10px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.left = Math.random() * window.innerWidth + 'px';
                    confetti.style.top = '-10px';
                    confetti.style.borderRadius = '50%';
                    confetti.style.pointerEvents = 'none';
                    confetti.style.zIndex = '9999';
                    confetti.style.animation = 'fall 3s linear';
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => confetti.remove(), 3000);
                }, i * 30);
            }
        }

        // Add CSS animation for confetti
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Trigger confetti on page load
        window.addEventListener('load', createConfetti);
    </script>
</body>
</html>

