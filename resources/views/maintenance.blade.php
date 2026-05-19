<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance Mode - Tracklyt</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .maintenance-container {
            max-width: 600px;
            width: 90%;
        }
        
        .maintenance-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .icon-wrapper {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .icon-wrapper i {
            font-size: 3rem;
            color: white;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 20px rgba(102, 126, 234, 0);
            }
        }
        
        h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        
        .lead {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 2rem;
            text-align: left;
        }
        
        .info-box h6 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .info-box p {
            color: #666;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .contact-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .contact-info p {
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .contact-info a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .contact-info a:hover {
            text-decoration: underline;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
            margin-bottom: 1rem;
            border-color: #667eea;
            border-right-color: transparent;
        }
        
        .footer-text {
            color: white;
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
        
        .footer-text a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-card">
            <!-- Icon -->
            <div class="icon-wrapper">
                <i class="bi bi-tools"></i>
            </div>
            
            <!-- Title -->
            <h1>We'll Be Back Soon!</h1>
            
            <!-- Message -->
            <p class="lead">
                Tracklyt is currently undergoing scheduled maintenance to improve your experience.
            </p>
            
            <!-- Spinner -->
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            
            <!-- Info Box -->
            <div class="info-box">
                <h6><i class="bi bi-info-circle me-2"></i>What's happening?</h6>
                <p>
                    We're performing system updates and improvements to enhance performance, 
                    security, and add new features. This maintenance is usually quick and 
                    shouldn't take long.
                </p>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info">
                <p>Need immediate assistance?</p>
                <p>
                    <i class="bi bi-envelope me-2"></i>
                    <a href="mailto:support@tracklyt.com">support@tracklyt.com</a>
                </p>
                <p class="mt-2 text-muted small">
                    <i class="bi bi-clock me-2"></i>
                    Expected completion: Within the next few minutes
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <p class="footer-text">
            &copy; {{ date('Y') }} Tracklyt. All rights reserved.
        </p>
    </div>
    
    <!-- Auto-refresh every 30 seconds -->
    <script>
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
