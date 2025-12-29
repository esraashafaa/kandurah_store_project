<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شحن المحفظة - Stripe Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .payment-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px;
        }
        .btn-stripe {
            background: linear-gradient(135deg, #635BFF 0%, #635BFF 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-stripe:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(99, 91, 255, 0.4);
        }
        .amount-btn {
            border: 2px solid #667eea;
            color: #667eea;
            font-weight: 600;
            transition: all 0.3s;
        }
        .amount-btn:hover, .amount-btn.active {
            background: #667eea;
            color: white;
        }
        .wallet-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Wallet Balance Info -->
                <div class="wallet-info text-center">
                    <h5 class="mb-2">
                        <i class="fas fa-wallet text-primary"></i>
                        رصيدك الحالي
                    </h5>
                    <h2 class="mb-0 text-primary">
                        ${{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}
                    </h2>
                </div>

                <!-- Payment Card -->
                <div class="card payment-card">
                    <div class="card-header text-center">
                        <h3 class="mb-1">
                            <i class="fas fa-credit-card"></i>
                            شحن المحفظة
                        </h3>
                        <p class="mb-0 small">الدفع الآمن عبر Stripe</p>
                    </div>
                    <div class="card-body p-4">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('stripe.checkout') }}" method="POST" id="paymentForm">
                            @csrf
                            
                            <!-- Quick Amount Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">اختر المبلغ السريع</label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <button type="button" class="btn amount-btn w-100" onclick="setAmount(10)">
                                            $10
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn amount-btn w-100" onclick="setAmount(25)">
                                            $25
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn amount-btn w-100" onclick="setAmount(50)">
                                            $50
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn amount-btn w-100" onclick="setAmount(100)">
                                            $100
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn amount-btn w-100" onclick="setAmount(250)">
                                            $250
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn amount-btn w-100" onclick="setAmount(500)">
                                            $500
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Amount Input -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-dollar-sign text-success"></i>
                                    أو أدخل مبلغ مخصص (USD)
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">$</span>
                                    <input 
                                        type="number" 
                                        name="amount" 
                                        id="amountInput"
                                        class="form-control @error('amount') is-invalid @enderror" 
                                        required 
                                        placeholder="أدخل المبلغ" 
                                        min="1" 
                                        step="0.01"
                                        value="{{ old('amount') }}">
                                </div>
                                @error('amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    الحد الأدنى: $1.00
                                </small>
                            </div>

                            <!-- Test Card Info -->
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-credit-card"></i>
                                    بطاقة الاختبار
                                </h6>
                                <p class="mb-1"><strong>رقم البطاقة:</strong> 4242 4242 4242 4242</p>
                                <p class="mb-1"><strong>CVC:</strong> أي 3 أرقام (مثال: 123)</p>
                                <p class="mb-1"><strong>تاريخ الانتهاء:</strong> أي تاريخ مستقبلي (مثال: 12/25)</p>
                                <p class="mb-0"><strong>ZIP:</strong> أي رقم (مثال: 12345)</p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-stripe btn-primary w-100 btn-lg">
                                <i class="fab fa-stripe"></i>
                                المتابعة للدفع عبر Stripe
                            </button>
                        </form>

                        <!-- Security Info -->
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-lock"></i>
                                جميع المدفوعات آمنة ومشفرة
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-right"></i>
                        العودة للوحة التحكم
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setAmount(amount) {
            document.getElementById('amountInput').value = amount;
            
            // Update active button
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>

