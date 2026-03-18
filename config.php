<?php
/**
 * Global Configuration for Civic Grievance System
 */

// 1. ENVIRONMENT SETTINGS
define('DEMO', 1); // 1 = Redirect all mail to TEST_EMAIL, 0 = Send to actual Gov Depts
define('TEST_EMAIL', 'srounincorp@gmail.com');

// 2. SMTP GMAIL CREDENTIALS
// Note: Use an App Password, NOT your regular Gmail password.
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'srounincorp@gmail.com'); 
define('SMTP_PASS', 'GMAIL SMTP OR GOOGLE APP PASSWORD'); 
define('SMTP_PORT', 587);

// 3. FILE PATHS
define('DIR_JSON', __DIR__ . '/data/directory.json');
define('COMPLAINTS_JSON', __DIR__ . '/data/complaints.json');
define('USERS_JSON', __DIR__ . '/data/users.json');

// 4. HELPER FUNCTIONS
/**
 * Saves data to a JSON file with exclusive locking to prevent corruption.
 */
function saveToJson($filename, $newData) {
    if (!file_exists($filename)) {
        file_put_contents($filename, json_encode([]));
    }
    
    $file = fopen($filename, 'c+');
    if (flock($file, LOCK_EX)) {
        $content = stream_get_contents($file);
        $data = json_decode($content, true) ?? [];
        $data[] = $newData;
        
        ftruncate($file, 0);
        rewind($file);
        fwrite($file, json_encode($data, JSON_PRETTY_PRINT));
        fflush($file);
        flock($file, LOCK_UN);
    }
    fclose($file);
}
// Add this to your config.php
define('SYSTEM_DISCLAIMER', "This system acts solely as a neutral intermediary. We do not verify the authenticity of complaints or IDs. The complainant holds 100% liability.");
/**
 * Reads and returns data from a JSON file.
 */
function readFromJson($filename) {
    if (!file_exists($filename)) return [];
    $content = file_get_contents($filename);
    return json_decode($content, true) ?? [];
}
?>
