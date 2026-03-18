<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// 1. Security Guard
if (!isset($_SESSION['user_id'])) {
    die("Access Denied: Please log in to file a grievance.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Load Fresh Identity Data from users.json (Safety check)
    $users = readFromJson(USERS_JSON);
    $currentUser = null;
    foreach ($users as $u) {
        if ($u['user_id'] == $_SESSION['user_id']) {
            $currentUser = $u;
            break;
        }
    }

    // 3. Load Routing Directory
    $directory = json_decode(file_get_contents(DIR_JSON), true);
    
    // 4. Capture & Sanitize Form Inputs
    $level       = $_POST['level'];
    $state       = $_POST['state'] ?? null;
    $dept_key    = $_POST['dept'];
    $district    = htmlspecialchars($_POST['district']);
    $pincode     = htmlspecialchars($_POST['pincode']);
    $subject_msg = htmlspecialchars($_POST['subject']);
    $description = htmlspecialchars($_POST['description']);
    
    // 5. Routing Logic (Finding the Email Address)
    if ($level === 'Union_Government') {
        $real_target = $directory['Union_Government'][$dept_key] ?? null;
        $dept_label = str_replace('_', ' ', $dept_key) . " (Govt of India)";
    } else {
        $real_target = $directory['States_UTs'][$state][$dept_key] ?? null;
        $dept_label = str_replace('_', ' ', $dept_key) . " ($state)";
    }

    if (!$real_target) {
        die("Routing Error: Department contact not found in directory.");
    }

    // 6. Identity Context (Aadhaar / ID)
    $id_type = $currentUser['id_type'] ?? 'Not Declared';
    $id_num  = $currentUser['id_number_declared'] ?? 'N/A';
    $is_matched = ($currentUser['match_status'] === 'NAME_MATCHED');

    // 7. Representation Logic
    $is_behalf = isset($_POST['on_behalf']);
    $v_name    = $is_behalf ? htmlspecialchars($_POST['v_name']) : $currentUser['name'];
    $v_contact = $is_behalf ? htmlspecialchars($_POST['v_contact']) : ($currentUser['phone'] ?? 'N/A');

    // 8. Mail Construction
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(SMTP_USER, 'CivicConnect Automated Dispatch');
        $mail->addAddress(DEMO ? TEST_EMAIL : $real_target);
        $mail->addReplyTo($_SESSION['user_email'], $_SESSION['user_name']);

        $mail->isHTML(true);
        $mail->Subject = (DEMO ? "[DEMO MODE] " : "") . "GRIEVANCE: " . $subject_msg;

        // Professional HTML Memorandum
        $body = "
        <div style='font-family: Arial, sans-serif; border: 2px solid #003366; padding: 30px; color: #1e293b; max-width: 650px; margin: auto;'>
            <div style='text-align: center; border-bottom: 2px solid #003366; padding-bottom: 15px; margin-bottom: 25px;'>
                <h2 style='color: #003366; margin: 0;'>OFFICIAL CITIZEN CORRESPONDENCE</h2>
                <p style='font-size: 11px; color: #64748b;'>Ref ID: GRV-" . time() . " | Dispatched via CivicConnect Gateway</p>
            </div>

            <table style='width: 100%; margin-bottom: 20px;'>
                <tr><td style='color:#64748b;'><b>To:</b></td><td>The Nodal Officer, $dept_label</td></tr>
                <tr><td style='color:#64748b;'><b>From:</b></td><td>{$_SESSION['user_name']}</td></tr>
                <tr><td style='color:#64748b;'><b>Date:</b></td><td>" . date('d-M-Y H:i') . "</td></tr>
            </table>

            <div style='background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>
                <strong style='font-size: 12px; color: #003366; text-transform: uppercase;'>Complainant Identity Details:</strong><br>
                <div style='margin-top: 5px;'>
                    ID Type: <b>$id_type</b> | ID Number: <b>$id_num</b><br>
                    Status: " . ($is_matched ? "<span style='color:#166534;'><b>✅ NAME-MATCHED PROFILE</b></span>" : "<span style='color:#991b1b;'>UNVERIFIED</span>") . "
                </div>
            </div>

            <p><b>SUBJECT:</b> " . strtoupper($subject_msg) . "</p>
            <p><b>LOCATION:</b> $district (PIN: $pincode)</p>";

        if ($is_behalf) {
            $body .= "
            <div style='border: 1px solid #fde68a; background: #fffbeb; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>
                <strong>REPRESENTATION NOTICE:</strong><br>
                Filing on behalf of: <b>$v_name</b> (Contact: $v_contact)
            </div>";
        }

        $body .= "
            <p><b>STATEMENT OF GRIEVANCE:</b><br>" . nl2br($description) . "</p>

            <div style='margin-top: 40px; padding: 15px; background: #fef2f2; border: 1px solid #fee2e2; font-size: 11px; color: #991b1b;'>
                <strong>LIABILITY DISCLAIMER:</strong><br>
                " . SYSTEM_DISCLAIMER . "
            </div>
            
            <hr style='border: 0; border-top: 1px solid #e2e8f0; margin-top: 30px;'>
            <p style='font-size: 10px; color: #94a3b8; text-align: center;'>
                This is a system-generated formal communication. Intended Recipient: $real_target
            </p>
        </div>";

        $mail->Body = $body;
        $mail->send();

        // 9. Log the successful dispatch
        $complaint_entry = [
            "ref_id"     => "GRV-" . time(),
            "user_id"    => $_SESSION['user_id'],
            "dept_label" => $dept_label,
            "subject"    => $subject_msg,
            "location"   => "$district - $pincode",
            "status"     => (DEMO ? "TEST_DISPATCHED" : "DISPATCHED"),
            "timestamp"  => date("Y-m-d H:i:s")
        ];
        saveToJson(COMPLAINTS_JSON, $complaint_entry);

        header("Location: dashboard.php?msg=success");
        exit();

    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        header("Location: dashboard.php?msg=error");
        exit();
    }
}