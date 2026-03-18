<?php
require_once 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CivicConnect | Automated Grievance Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hero { background: #003366; color: white; padding: 60px 20px; text-align: center; border-radius: 0 0 50px 50px; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, min-minmax(250px, 1fr)); gap: 20px; margin-top: 40px; }
        .feature-card { background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; border: 1px solid #eee; }
        .cta-box { margin-top: 30px; }
        .disclaimer-strip { background: #fff3cd; color: #856404; padding: 15px; text-align: center; font-size: 13px; border-bottom: 1px solid #ffeeba; }
    </style>
</head>
<body>

<div class="disclaimer-strip">
    <strong>Notice:</strong> This portal is an independent intermediary. We do not verify or cross-check grievances. 
    Full liability remains with the complainant.
</div>

<div class="hero">
    <h1>CivicConnect India</h1>
    <p>Automated Professional Correspondence with Union & State Departments</p>
    
    <div class="cta-box">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn" style="background:#ff9933">Go to My Dashboard</a>
        <?php else: ?>
            <a href="login.php" class="btn" style="background:white; color:#003366; margin-right:10px;">Login</a>
            <a href="register.php" class="btn" style="background:#ff9933">Register Now</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <div class="feature-grid">
        <div class="feature-card">
            <h3>Direct Routing</h3>
            <p>Automatically identifies and emails the correct Nodal Officer for your specific department and state.</p>
        </div>
        <div class="feature-card">
            <h3>On-Behalf Filing</h3>
            <p>Empowering citizens to file grievances for those without tech access. System acts as a digital postman.</p>
        </div>
        <div class="feature-card">
            <h3>Verified Identity</h3>
            <p>Self-declare your Government ID data to add a professional 'Matched Profile' badge to your correspondence.</p>
        </div>
    </div>

    <section style="margin-top: 60px; padding: 40px; background: #f8fafc; border-radius: 15px; text-align: center;">
        <h2>The Middleman Protocol</h2>
        <p style="max-width: 700px; margin: auto; color: #64748b;">
            CivicConnect does not store photos or scans. We match your registered name against your declared ID data to provide a trust-score for the receiving department. 
            All replies from the Government go <strong>directly to your inbox</strong> via our Reply-To technology.
        </p>
    </section>
</div>

<footer style="text-align:center; padding: 40px; color: #888; font-size: 12px;">
    &copy; 2026 CivicConnect India. Powered by PHP/JSON Intermediary Logic.
</footer>

</body>
</html>