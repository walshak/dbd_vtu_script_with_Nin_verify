<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - {{ $transaction->reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
        }
        .receipt-body {
            padding: 30px;
        }
        .receipt-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-title h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .transaction-details {
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }
        .detail-label {
            font-weight: 500;
            color: #666;
        }
        .detail-value {
            font-weight: 600;
            color: #333;
        }
        .amount-highlight {
            color: #27ae60;
            font-size: 18px;
        }
        .receipt-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            border-top: 1px solid #eee;
        }
        .footer-info {
            text-align: center;
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }
        .company-details {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .reference-code {
            font-family: 'Courier New', monospace;
            background-color: #f1f3f4;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="company-tagline">Reliable VTU Services</div>
        </div>

        <!-- Body -->
        <div class="receipt-body">
            <div class="receipt-title">
                <h2>Transaction Receipt</h2>
                <span class="status-badge status-{{ strtolower($transaction->status) === 'completed' ? 'success' : (strtolower($transaction->status) === 'pending' ? 'pending' : 'failed') }}">
                    {{ $transaction->status }}
                </span>
            </div>

            <div class="transaction-details">
                <div class="detail-row">
                    <span class="detail-label">Reference Number:</span>
                    <span class="detail-value reference-code">{{ $transaction->reference }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Service Type:</span>
                    <span class="detail-value">{{ ucfirst($transaction->service_type) }}</span>
                </div>

                @if(!empty($transaction->phone))
                <div class="detail-row">
                    <span class="detail-label">Phone Number:</span>
                    <span class="detail-value">{{ $transaction->phone }}</span>
                </div>
                @endif

                @if(!empty($transaction->network))
                <div class="detail-row">
                    <span class="detail-label">Network:</span>
                    <span class="detail-value">{{ strtoupper($transaction->network) }}</span>
                </div>
                @endif

                @if(!empty($transaction->recipient))
                <div class="detail-row">
                    <span class="detail-label">Recipient:</span>
                    <span class="detail-value">{{ $transaction->recipient }}</span>
                </div>
                @endif

                @if(!empty($transaction->service_id))
                <div class="detail-row">
                    <span class="detail-label">Service Provider:</span>
                    <span class="detail-value">{{ $transaction->service_id }}</span>
                </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Date & Time:</span>
                    <span class="detail-value">{{ $transaction->created_at->format('M d, Y - h:i A') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Customer Name:</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Customer Email:</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Amount Paid:</span>
                    <span class="detail-value amount-highlight">₦{{ number_format($transaction->amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="footer-info">
                <strong>Thank you for using our services!</strong><br>
                This is a computer-generated receipt and does not require a signature.<br>
                For support, contact us at {{ $company['email'] }} or {{ $company['phone'] }}
            </div>

            <div class="company-details">
                Generated on {{ $generated_at->format('M d, Y - h:i A') }}<br>
                {{ $company['name'] }} - Your Trusted VTU Partner
            </div>
        </div>
    </div>
</body>
</html>
