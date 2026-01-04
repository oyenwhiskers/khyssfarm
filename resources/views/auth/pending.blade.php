<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Under Review - KHYSS Farm</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .pending-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            margin: 20px;
            overflow: hidden;
        }
        
        .pending-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .pending-icon {
            font-size: 64px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .pending-header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 600;
        }
        
        .pending-body {
            padding: 40px;
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            padding: 12px 20px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .pending-message {
            color: #555;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .info-box h5 {
            color: #667eea;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .info-box p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            transition: transform 0.2s;
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="pending-container">
        <div class="pending-header">
            <div class="pending-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <h1>Account Under Review</h1>
        </div>
        
        <div class="pending-body">
            <div class="status-badge">
                <i class="fas fa-clock me-2"></i>PENDING APPROVAL
            </div>
            
            <p class="pending-message">
                Thank you for creating an account with KHYSS Farm! Your account is currently under review by our administration team.
            </p>
            
            <div class="info-box">
                <h5>
                    <i class="fas fa-info-circle me-2"></i>What's happening?
                </h5>
                <p>
                    We review all new accounts to ensure the security and integrity of our system. This process typically takes 24-48 hours.
                </p>
            </div>
            
            <div class="info-box">
                <h5>
                    <i class="fas fa-envelope me-2"></i>You'll receive an email
                </h5>
                <p>
                    Once your account is approved, we'll send you a confirmation email. You'll then be able to log in and access all features.
                </p>
            </div>
            
            <div class="info-box">
                <h5>
                    <i class="fas fa-user me-2"></i>Your Account
                </h5>
                <p>
                    <strong>Name:</strong> {{ Auth::user()->name }}<br>
                    <strong>Email:</strong> {{ Auth::user()->email }}<br>
                    <strong>Applied:</strong> {{ Auth::user()->created_at->format('F d, Y H:i') }}
                </p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
            
            <p class="text-muted mt-4" style="font-size: 12px;">
                If you have any questions, please contact our support team.
            </p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
