<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$directory_json = file_get_contents(DIR_JSON);
$is_verified = (isset($_SESSION['match_status']) && $_SESSION['match_status'] === 'NAME_MATCHED');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Grievance | CivicConnect</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .step-header { display: flex; align-items: center; margin-bottom: 20px; color: var(--navy); border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .step-num { background: var(--navy); color: white; width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 14px; }
        
        /* Live Preview Styling */
        .email-preview { background: #f1f5f9; border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-size: 13px; }
        .preview-label { font-weight: bold; color: #64748b; text-transform: uppercase; font-size: 10px; display: block; margin-bottom: 5px; }
        
        .behalf-section { display: none; background: #fffbeb; border: 1px solid #fef3c7; padding: 15px; border-radius: 8px; margin-top: 10px; }
        
        textarea { resize: vertical; min-height: 120px; }
    </style>
</head>
<body>

<div class="nav-bar">
    <span>CivicConnect Portal</span>
    <a href="dashboard.php" class="logout-link" style="color:white;">← Back</a>
</div>

<div class="container" style="max-width: 750px;">
    <div class="card">
        <h2 style="margin-top:0;">Draft Formal Grievance</h2>

        <form action="process_complaint.php" method="POST" id="grievanceForm">
            <div class="step-header">
                <span class="step-num">1</span> <strong>Target Authority</strong>
            </div>
            
            <div class="grid">
                <div class="form-group">
                    <label>Jurisdiction</label>
                    <select name="level" id="level" onchange="updateDropdowns()" required>
                        <option value="">-- Select --</option>
                        <option value="Union_Government">Union (Central Govt)</option>
                        <option value="States_UTs">State / UT Govt</option>
                    </select>
                </div>

                <div class="form-group" id="state_group" style="display:none;">
                    <label>State/Union Territory</label>
                    <select name="state" id="state" onchange="updateDepartments()">
                        <option value="">-- Select State --</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label>Department / Ministry</label>
                    <select name="dept" id="dept" required onchange="updatePreview()">
                        <option value="">Select Level First...</option>
                    </select>
                </div>
            </div>

            <div class="step-header" style="margin-top:30px;">
                <span class="step-num">2</span> <strong>Grievance Details</strong>
            </div>

            <div class="form-group">
                <label>Subject Line</label>
                <input type="text" name="subject" id="subject" placeholder="Summarize the issue in one sentence" required onkeyup="updatePreview()">
            </div>

            <div class="grid">
                <div class="form-group">
                    <label>District</label>
                    <input type="text" name="district" placeholder="e.g. Nagpur" required>
                </div>
                <div class="form-group">
                    <label>Pincode</label>
                    <input type="text" name="pincode" pattern="[0-9]{6}" placeholder="6 Digits" required>
                </div>
            </div>

            <div class="form-group">
                <label>Description of Issue</label>
                <textarea name="description" placeholder="Provide specific dates, locations, and facts..." required></textarea>
            </div>

            <div class="step-header" style="margin-top:30px;">
                <span class="step-num">3</span> <strong>Representation</strong>
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; cursor:pointer;">
                    <input type="checkbox" name="on_behalf" id="on_behalf" onclick="toggleBehalf()" style="width:auto; margin-right:10px;">
                    <span>Filing on behalf of another citizen (Victim)</span>
                </label>
            </div>

            <div id="behalf_section" class="behalf-section">
                <div class="grid">
                    <div class="form-group">
                        <label>Victim's Full Name</label>
                        <input type="text" name="v_name" placeholder="Name on their ID">
                    </div>
                    <div class="form-group">
                        <label>Victim's Contact (Optional)</label>
                        <input type="text" name="v_contact" placeholder="Mobile No.">
                    </div>
                </div>
            </div>

            <div class="email-preview">
                <span class="preview-label">Outgoing Email Preview</span>
                <div id="preview_content">
                    <strong>To:</strong> <span id="pre_dept" style="color:#003366;">[Recipient]</span><br>
                    <strong>Subject:</strong> <span id="pre_subj" style="color:#003366;">[Your Subject]</span><br>
                    <strong>Identity:</strong> <?php echo $is_verified ? '<span style="color:green;">Verified Match Attached</span>' : '<span style="color:red;">Anonymous/Unverified</span>'; ?>
                </div>
            </div>

            <div style="font-size: 11px; color: #64748b; margin-bottom: 20px;">
                <input type="checkbox" required> I accept full legal liability for the accuracy of this complaint.
            </div>

            <button type="submit" class="btn">Dispatch Professional Email</button>
        </form>
    </div>
</div>

<script>
const directory = <?php echo $directory_json; ?>;

function updateDropdowns() {
    const level = document.getElementById('level').value;
    const stateGroup = document.getElementById('state_group');
    const stateSelect = document.getElementById('state');
    const deptSelect = document.getElementById('dept');

    stateGroup.style.display = (level === 'States_UTs') ? 'block' : 'none';
    stateSelect.innerHTML = '<option value="">-- Select State --</option>';
    deptSelect.innerHTML = '<option value="">-- Select Dept --</option>';

    if (level === 'Union_Government') {
        Object.keys(directory['Union_Government']).forEach(key => {
            deptSelect.options.add(new Option(key.replace(/_/g, ' '), key));
        });
    } else if (level === 'States_UTs') {
        Object.keys(directory['States_UTs']).forEach(state => {
            stateSelect.options.add(new Option(state.replace(/_/g, ' '), state));
        });
    }
    updatePreview();
}

function updateDepartments() {
    const stateName = document.getElementById('state').value;
    const deptSelect = document.getElementById('dept');
    deptSelect.innerHTML = '<option value="">-- Select Dept --</option>';

    if (stateName) {
        Object.keys(directory['States_UTs'][stateName]).forEach(key => {
            deptSelect.options.add(new Option(key.replace(/_/g, ' '), key));
        });
    }
    updatePreview();
}

function updatePreview() {
    const dept = document.getElementById('dept').value;
    const subj = document.getElementById('subject').value;
    document.getElementById('pre_dept').innerText = dept ? dept.replace(/_/g, ' ') : '[Recipient]';
    document.getElementById('pre_subj').innerText = subj || '[Your Subject]';
}

function toggleBehalf() {
    document.getElementById('behalf_section').style.display = 
        document.getElementById('on_behalf').checked ? 'block' : 'none';
}
</script>

</body>
</html>