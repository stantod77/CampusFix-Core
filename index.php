<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusFix | Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: -apple-system, sans-serif; margin: 0; }
        
        .navbar-custom {
            background-color: #ffffff;
            padding: 12px 24px;
            border-bottom: 2px solid #0b1f3a;
            display: flex;
            align-items: center;
        }
        .logo-box {
            background-color: #0b1f3a; color: white;
            padding: 2px 8px; border-radius: 4px;
            font-weight: bold; font-size: 1.1rem; margin-right: 10px;
        }
        .logo-text { color: #0b1f3a; font-weight: bold; font-size: 1.3rem; }

        .hero-section {
            background-color: #ffffff;
            padding: 60px 20px 40px 20px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 40px;
        }
        .hero-title { color: #0b1f3a; font-weight: 800; font-size: 2.5rem; margin-bottom: 10px; }
        .hero-subtitle { color: #6c757d; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }

        .gateway-card {
            border: none; border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: #ffffff; height: 100%;
            border-top: 5px solid #0b1f3a;
            transition: transform 0.2s ease;
        }
        .gateway-card:hover { transform: translateY(-5px); }
        
        .btn-navy {
            background-color: #0b1f3a;
            color: white; border: none;
            padding: 12px; font-weight: bold;
            border-radius: 4px; width: 100%;
            text-decoration: none; display: inline-block;
        }
        .btn-navy:hover { background-color: #1a3a61; color: white; }
        
        .card-title { color: #0b1f3a; font-weight: bold; }
        .system-footer { margin-top: 80px; padding-bottom: 30px; text-align: center; color: #adb5bd; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="navbar-custom">
    <div class="logo-box">CF</div>
    <div class="logo-text">CampusFix</div>
</div>

<div class="hero-section">
    <h1 class="hero-title">CampusFix Service Portal</h1>
    <p class="hero-subtitle">
        Centralized management for campus facility requests and system infrastructure monitoring. 
    </p>
</div>

<div class="container">
    <div class="row g-4 justify-content-center">
        
        <div class="col-md-4">
            <div class="card gateway-card p-4 text-center">
                <h4 class="card-title mb-3">Facility Request</h4>
                <p class="text-muted small mb-4">Report new maintenance or infrastructure issues to the CIT team.</p>
                <div class="mt-auto">
                    <a href="ticket.html" class="btn btn-navy">Submit Ticket</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card gateway-card p-4 text-center">
                <h4 class="card-title mb-3">Staff Dashboard</h4>
                <p class="text-muted small mb-4">Authorized access for ticket management and integrity logs.</p>
                <div class="mt-auto">
                    <a href="login.html" class="btn btn-navy">Admin Login</a>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="system-footer">
    © 2026 CampusFix Core · All rights reserved.
</div>

</body>
</html>
