<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idName   = strtoupper(trim($_POST['id_name']));
    $idType   = htmlspecialchars($_POST['id_type']);
    $idNumber = htmlspecialchars($_POST['id_number']);
    $profileName = strtoupper(trim($_SESSION['user_name']));

    // Determine Match Status
    $status = ($idName === $profileName) ? "NAME_MATCHED" : "NAME_MISMATCH";

    // Update Users JSON
    $users = readFromJson(USERS_JSON);
    foreach ($users as &$user) {
        if ($user['user_id'] == $_SESSION['user_id']) {
            $user['id_type'] = $idType;
            $user['id_number_declared'] = $idNumber;
            $user['match_status'] = $status;
            
            // Sync Session
            $_SESSION['match_status'] = $status;
            $_SESSION['id_type'] = $idType;
            $_SESSION['id_number_declared'] = $idNumber;
            break;
        }
    }
    
    file_put_contents(USERS_JSON, json_encode($users, JSON_PRETTY_PRINT), LOCK_EX);

    // Return success to the JavaScript
    echo json_encode(['status' => 'success', 'match' => $status]);
    exit();
}