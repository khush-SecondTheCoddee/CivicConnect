<?php
require_once 'config.php';
session_start();

$search_id = isset($_GET['ref']) ? trim(htmlspecialchars($_GET['ref'])) : '';
$found_record = null;

if ($search_id) {
    // Helper function from config.php to read the JSON array
    $all_complaints = readFromJson(COMPLAINTS_JSON);
    
    if (is_array($all_complaints)) {
        foreach ($all_complaints as $complaint) {
            // Case-insensitive comparison to ensure 'grv' matches 'GRV'
            if (strcasecmp($complaint['ref_id'], $search_id) == 0) {
                $found_record = $complaint;
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Grievance | CivicConnect</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .result-card { margin-top: 30px; border-top: 5px solid var(--navy); }
        .data-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
        .data-label { color: #64748b; font-size: 13px; font-weight: 600; }
        .data-value { color: var(--navy); font-weight: 500; }
        .status-tag { padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .status-live { background: #dcfce7; color: #166534; }
        .status-test { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

<div class="nav-bar">
    <span>CivicConnect Dispatch Verification</span>
    <a href="index.php" class="logout-link" style="color:white;">Home</a>
</div>

<div class="container" style="max-width: 600px;">
    <div class="card text-center">
        <h2>Verify Dispatch</h2>
        <p>Enter Reference ID (e.g., GRV-1773833067)</p>
        
        <form action="verify_complaint.php" method="GET" style="display:flex; gap:10px; margin-top:15px;">
            <input type="text" name="ref" value="<?php echo $search_id; ?>" placeholder="Enter Ref ID..." required>
            <button type="submit" class="btn" style="width:auto;">Verify</button>
        </form>
    </div>

    <?php if ($search_id): ?>
        <?php if ($found_record): ?>
            <?php 
                // Detect Test vs Live based on your JSON status
                $is_test = (strpos($found_record['status'], 'TEST') !== false);
            ?>
            <div class="card result-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h3 style="margin:0;">Dispatch Record</h3>
                    <span class="status-tag <?php echo $is_test ? 'status-test' : 'status-live'; ?>">
                        <?php echo $is_test ? 'SIMULATED / TEST' : 'OFFICIAL DISPATCH'; ?>
                    </span>
                </div>

                <div class="data-row">
                    <span class="data-label">Reference ID</span>
                    <span class="data-value"><?php echo $found_record['ref_id']; ?></span>
                </div>

                <div class="data-row">
                    <span class="data-label">Authority</span>
                    <span class="data-value"><?php echo $found_record['dept_label']; ?></span>
                </div>

                <div class="data-row">
                    <span class="data-label">Subject</span>
                    <span class="data-value"><?php echo htmlspecialchars($found_record['subject']); ?></span>
                </div>

                <div class="data-row">
                    <span class="data-label">Location</span>
                    <span class="data-value"><?php echo htmlspecialchars($found_record['location']); ?></span>
                </div>

                <div class="data-row" style="border:none;">
                    <span class="data-label">Date Processed</span>
                    <span class="data-value"><?php echo date("d M Y, h:i A", strtotime($found_record['timestamp'])); ?></span>
                </div>

                <?php if ($is_test): ?>
                <div style="background:#fff1f2; color:#9f1239; padding:10px; border-radius:5px; font-size:12px; margin-top:15px;">
                    <strong>Note:</strong> This record was generated in Test Mode. No actual email was sent to government authorities.
                </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="card text-center" style="border:1px solid var(--danger);">
                <p style="color:var(--danger); font-weight:bold;">No Record Found</p>
                <p style="font-size:13px;">The ID "<?php echo $search_id; ?>" does not exist in our dispatch logs.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>