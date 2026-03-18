<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$registered_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link Identity | CivicConnect</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .save-indicator { font-size: 12px; margin-top: 5px; display: none; }
        .match-check { background: #f8fafc; padding: 15px; border: 1px dashed #cbd5e1; border-radius: 6px; margin: 15px 0; }
    </style>
</head>
<body>

<div class="container" style="max-width: 500px; margin-top: 50px;">
    <div class="card">
        <h2>Link Identity</h2>
        <p style="font-size: 13px; color: #666;">Data is saved automatically as you type and click away.</p>

        <form id="idForm">
            <div class="form-group">
                <label>ID Type</label>
                <select name="id_type" onchange="autoSave()">
                    <option value="Aadhaar Card">Aadhaar Card</option>
                    <option value="Voter ID">Voter ID</option>
                    <option value="PAN Card">PAN Card</option>
                </select>
            </div>

            <div class="form-group">
                <label>Name on ID (Must match profile)</label>
                <input type="text" name="id_name" id="id_name" onblur="autoSave()" placeholder="Full Name">
                <div id="match_note" class="save-indicator"></div>
            </div>

            <div class="form-group">
                <label>ID Number</label>
                <input type="text" name="id_number" onblur="autoSave()" placeholder="Enter ID Number">
                <div id="save_note" class="save-indicator" style="color: #28a745;">✓ Saved automatically</div>
            </div>
        </form>
        
        <br>
        <a href="dashboard.php" class="btn" style="width:100%; box-sizing: border-box;">Go to Dashboard</a>
    </div>
</div>

<script>
function autoSave() {
    const formData = new FormData(document.getElementById('idForm'));
    const saveNote = document.getElementById('save_note');
    const matchNote = document.getElementById('match_note');
    const profileName = "<?php echo addslashes(strtoupper($registered_name)); ?>";
    const enteredName = document.getElementById('id_name').value.toUpperCase();

    // Visual Match Check
    if(enteredName !== "") {
        matchNote.style.display = 'block';
        if(enteredName === profileName) {
            matchNote.innerText = "✓ Name Matches Profile";
            matchNote.style.color = "#28a745";
        } else {
            matchNote.innerText = "× Name Mismatch";
            matchNote.style.color = "#dc3545";
        }
    }

    // AJAX Call to save data in background
    fetch('process_match.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            saveNote.style.display = 'block';
            setTimeout(() => { saveNote.style.display = 'none'; }, 2000);
        }
    });
}
</script>

</body>
</html>