<?php
require_once 'config.php';
session_start();

// Guard: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all complaints and filter for the logged-in user
$all_complaints = readFromJson(COMPLAINTS_JSON);
$user_id = $_SESSION['user_id'];

$my_complaints = array_filter($all_complaints, function($item) use ($user_id) {
    return $item['user_id'] == $user_id;
});

// Sort: Newest first
usort($my_complaints, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});

// Check Match Status for the Header
$match_status = $_SESSION['match_status'] ?? 'UNVERIFIED';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard | CivicConnect</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .nav-bar { background: #003366; color: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
        .status-card { background: #fff; border-left: 5px solid #ff9933; padding: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-verified { background: #e8f5e9; color: #2e7d32; }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
    </style>
</head>
<body>

<div class="nav-bar">
    <strong>CivicConnect Dashboard</strong>
    <div>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" style="color: #ff9933; margin-left: 20px; text-decoration: none; font-size: 14px;">Logout</a>
    </div>
</div>

<div class="container">
    
    <div class="status-card">
        <div>
            <h4 style="margin:0;">Identity Match Status</h4>
            <p style="margin:5px 0 0; font-size: 13px; color: #666;">
                <?php echo ($match_status === 'NAME_MATCHED') ? "Your profile is cross-matched with your declared ID." : "Declare your ID details to improve grievance priority."; ?>
            </p>
        </div>
        <div>
            <?php if($match_status === 'NAME_MATCHED'): ?>
                <span class="badge badge-verified">✓ Matched Profile</span>
            <?php else: ?>
                <a href="verify_id.php" class="btn btn-sm" style="background:#ff9933">Link ID Now</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <h3>My Dispatched Grievances</h3>
            <a href="log_complaint.php" class="btn">+ File New Complaint</a>
        </div>

        <?php if(empty($my_complaints)): ?>
            <p style="text-align:center; color:#999; padding: 40px;">No complaints filed yet. Your history will appear here.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Ref ID</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($my_complaints as $complaint): ?>
                    <tr>
                        <td><?php echo date("d M Y", strtotime($complaint['timestamp'])); ?></td>
                        <td><code><?php echo $complaint['ref_id']; ?></code></td>
                        <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                        <td>
                            <span class="badge" style="background:#e3f2fd; color:#0d47a1;">
                                <?php echo $complaint['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <p style="margin-top: 40px; font-size: 11px; color: #999; text-align: center;">
        <?php echo SYSTEM_DISCLAIMER; ?>
    </p>
</div>

</body>
</html>