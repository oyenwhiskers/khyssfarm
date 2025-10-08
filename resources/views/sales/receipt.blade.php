<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->id }} - KHYSS Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .receipt-container { 
                max-width: none !important; 
                margin: 0 !important; 
                padding: 12px !important;
            }
            body { background: white !important; }
            .farm-header { 
                background: linear-gradient(135deg, #28a745, #20c997) !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                padding: 12px !important; 
                margin-bottom: 12px !important;
            }
            .total-section {
                background: linear-gradient(135deg, #28a745, #20c997) !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                padding: 10px !important;
            }
            .items-table th {
                background: linear-gradient(135deg, #007bff, #0056b3) !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                padding: 8px !important;
            }
            .items-table td {
                padding: 8px !important;
            }
            .status-paid {
                background: #d4edda !important;
                color: #155724 !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .status-pending {
                background: #fff3cd !important;
                color: #856404 !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .status-partial {
                background: #f8d7da !important;
                color: #721c24 !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .contact-info {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                padding: 6px !important; 
                margin: 5px 0 !important;
                border: 1px solid #dee2e6 !important;
                border-radius: 10px !important;
            }
            /* Better spaced print layout */
            .info-section { 
                padding: 10px !important; 
                margin-bottom: 10px !important;
                background: #f8f9fa !important;
                border: 1px solid #dee2e6 !important;
                border-radius: 10px !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .items-table { margin-bottom: 10px !important; }
            .footer-section { margin-top: 8px !important; padding-top: 8px !important; }
            .receipt-title { font-size: 1.3rem !important; margin-bottom: 6px !important; }
            .total-amount { 
                font-size: 1.8rem !important; 
                margin-bottom: 0 !important;
                text-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
            }
            h6 { font-size: 0.9rem !important; margin-bottom: 6px !important; }
            .info-row { padding: 4px 0 !important; }
            .contact-info .col-4 { padding: 3px !important; }
            .contact-info i { font-size: 0.75rem !important; margin-bottom: 2px !important; }
            .contact-info strong { font-size: 0.7rem !important; line-height: 1.2 !important; }
            .contact-info small { font-size: 0.6rem !important; }
            .contact-info br { line-height: 1 !important; }
            p { margin-bottom: 4px !important; }
            small { font-size: 0.7rem !important; }
            .payment-status { 
                padding: 4px 12px !important; 
                font-size: 0.65rem !important; 
                border-radius: 15px !important;
            }
        }
        
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 18px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .farm-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 18px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 18px;
        }
        
        .receipt-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .receipt-number {
            background: rgba(255,255,255,0.2);
            padding: 6px 12px;
            border-radius: 25px;
            display: inline-block;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .info-value {
            color: #212529;
            font-size: 0.9rem;
        }
        
        .items-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
            border: 2px solid #e9ecef;
        }
        
        .items-table th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            padding: 10px;
            border: none;
            font-size: 0.9rem;
        }
        
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.9rem;
        }
        
        .total-section {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        
        .total-amount {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .payment-status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }
        
        .status-partial {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        
        .footer-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 12px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
        }
        
        .contact-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
            margin: 12px 0;
        }
        
        .contact-info i {
            font-size: 1rem;
            margin-bottom: 4px;
        }
        
        .contact-info strong {
            color: #212529;
            font-size: 0.8rem;
            line-height: 1.3;
        }
        
        .contact-info small {
            color: #6c757d;
            font-size: 0.65rem;
        }
        
        .print-btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-right: 15px;
            transition: all 0.3s ease;
        }
        
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
            color: white;
        }
        
        .back-btn {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108,117,125,0.3);
            color: white;
        }
    </style>
</head>
<body style="background: #f8f9fa; padding: 20px 0;">
    <div class="receipt-container">
        <!-- Farm Header -->
        <div class="farm-header">
            <div class="receipt-title">
                <i class="fas fa-pepper-hot me-2"></i>
                KHYSS Chili Farm
            </div>
            <p class="mb-3">Premium Quality Chili Producer</p>
            <div class="receipt-number">
                Receipt #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <!-- Customer Information -->
        <div class="info-section">
            <h6 class="mb-2">
                <i class="fas fa-user me-2 text-primary"></i>
                Customer Information
            </h6>
            <div class="info-row">
                <span class="info-label">Customer Name:</span>
                <span class="info-value">{{ $sale->customer->name }}</span>
            </div>
            @if($sale->customer->email)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $sale->customer->email }}</span>
            </div>
            @endif
            @if($sale->customer->phone)
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $sale->customer->phone }}</span>
            </div>
            @endif
            @if($sale->customer->location)
            <div class="info-row">
                <span class="info-label">Location:</span>
                <span class="info-value">{{ $sale->customer->location }}</span>
            </div>
            @endif
        </div>

        <!-- Sale Information -->
        <div class="info-section">
            <h6 class="mb-2">
                <i class="fas fa-receipt me-2 text-success"></i>
                Sale Information
            </h6>
            <div class="info-row">
                <span class="info-label">Sale Date:</span>
                <span class="info-value">{{ $sale->sale_date->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Receipt Date:</span>
                <span class="info-value">{{ now()->format('F d, Y H:i A') }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="items-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Unit Price</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $sale->variety }} Chili</strong>
                            <br>
                            <small class="text-muted">Premium Quality</small>
                        </td>
                        <td class="text-center">
                            <strong>{{ number_format($sale->quantity_kg, 2) }} kg</strong>
                        </td>
                        <td class="text-center">
                            <strong>RM{{ number_format($sale->price_per_kg, 2) }}</strong>
                            <br>
                            <small class="text-muted">per kg</small>
                        </td>
                        <td class="text-center">
                            <strong>RM{{ number_format($sale->total_amount, 2) }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total Section -->
        <div class="total-section">
            <p class="mb-2" style="font-size: 0.9rem; opacity: 0.9;">Total Amount</p>
            <div class="total-amount">
                RM{{ number_format($sale->total_amount, 2) }}
            </div>
            <div class="payment-status status-{{ $sale->payment_status }} mt-3">
                <i class="fas fa-{{ $sale->payment_status === 'paid' ? 'check-circle' : ($sale->payment_status === 'pending' ? 'clock' : 'exclamation-triangle') }} me-1"></i>
                {{ ucfirst($sale->payment_status) }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <p class="mb-3">
                <strong>Thank you for choosing KHYSS Chili Farm!</strong>
            </p>
            
            <!-- Contact Information -->
            <div class="contact-info mb-2">
                <div class="row text-center">
                    <div class="col-4">
                        <i class="fas fa-user text-primary"></i><br>
                        <strong>Mohd Yusrim</strong><br>
                        <small>Farm Owner</small>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-phone text-success"></i><br>
                        <strong>010-807 2584</strong><br>
                        <small>Contact Number</small>
                    </div>
                    <div class="col-4">
                        <i class="fab fa-facebook text-info"></i><br>
                        <strong>KHYSS Farm</strong><br>
                        <small>Pembekal Cili Padi</small>
                    </div>
                </div>
            </div>
            
            <p class="mb-0">
                <small>
                    Follow us on Facebook for updates on fresh chili availability.<br>
                    This is a computer-generated receipt and is valid without signature.
                </small>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="print-btn">
                <i class="fas fa-print me-2"></i>
                Print Receipt
            </button>
            <a href="{{ route('sales.index') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Sales
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>