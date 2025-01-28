<?php
// Database credentials
$servername = "localhost";
$username = "semaphore_user";
$password = "DFyuqwhjty34JK@#23@#";
$dbname = "semaphore_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $response = ['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error];
    echo json_encode($response);
    die();
}

// Read POST data
$data = file_get_contents('php://input');
$data = json_decode($data, true);

// Initialize response
$response = ['status' => 'error', 'message' => 'Invalid data'];

// Sanity check function
function checkAndSetDefault(&$array, $key, $default, &$missing_fields, $allow_empty_string = false) {
    if (!isset($array[$key]) || $array[$key] === null || (!$allow_empty_string && $array[$key] === '')) {
        $array[$key] = $default;
        $missing_fields[] = $key;
        return false;
    }
    return true;
}

// Check if data exists and has all required fields
$missing_fields = [];
if ($data && isset($data['pending_updates']) && isset($data['installed_updates']) && isset($data['hostname']) && isset($data['project_id']) && isset($data['task_id'])) {
    $hostname = $data['hostname'];
    $project_id = $data['project_id'];
    $task_id = $data['task_id'];
    $pending_updates = json_decode($data['pending_updates'], true);
    $installed_updates = json_decode($data['installed_updates'], true);

    // Parse installed updates
    $parsed_installed_updates = [];
    foreach ($installed_updates as $update) {
        $parts = preg_split('/\s{2,}/', trim($update));
        if (count($parts) === 3) {
            $parsed_installed_updates[] = [
                'name' => $parts[0],
                'version' => $parts[1],
                'repo' => $parts[2]
            ];
        }
    }

    // Parse pending updates
    $parsed_pending_updates = [];
    foreach ($pending_updates as $update) {
        $parts = preg_split('/\s{2,}/', trim($update));
        if (count($parts) === 3) {
            $parsed_pending_updates[] = [
                'name' => $parts[0],
                'version' => $parts[1],
                'repo' => $parts[2]
            ];
        }
    }

    $inserted_count = 0;
    $updated_count = 0;

    // Insert pending updates into the database
    foreach ($parsed_pending_updates as $update) {
        $stmt = $conn->prepare("INSERT INTO linux_pending_updates (hostname, update_name, version, repo, project_id, task_id) VALUES (?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE update_name=VALUES(update_name), version=VALUES(version), repo=VALUES(repo), project_id=VALUES(project_id), task_id=VALUES(task_id), timestamp=CURRENT_TIMESTAMP");
        if ($stmt === false) {
            $response['message'] = "Prepare failed: " . $conn->error;
            echo json_encode($response);
            die();
        }
        $stmt->bind_param("ssssii", $hostname, $update['name'], $update['version'], $update['repo'], $project_id, $task_id);
        if (!$stmt->execute()) {
            $response['message'] = "Execute failed: " . $stmt->error;
            echo json_encode($response);
            die();
        }
        if ($stmt->affected_rows === 1) {
            $inserted_count++;
        } elseif ($stmt->affected_rows === 2) {
            $updated_count++;
        }
        $stmt->close();
    }

    // Insert installed updates into the database
    foreach ($parsed_installed_updates as $update) {
        $stmt = $conn->prepare("INSERT INTO linux_installed_updates (hostname, update_name, version, repo, project_id, task_id) VALUES (?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE update_name=VALUES(update_name), version=VALUES(version), repo=VALUES(repo), project_id=VALUES(project_id), task_id=VALUES(task_id), timestamp=CURRENT_TIMESTAMP");
        if ($stmt === false) {
            $response['message'] = "Prepare failed: " . $conn->error;
            echo json_encode($response);
            die();
        }
        $stmt->bind_param("ssssii", $hostname, $update['name'], $update['version'], $update['repo'], $project_id, $task_id);
        if (!$stmt->execute()) {
            $response['message'] = "Execute failed: " . $stmt->error;
            echo json_encode($response);
            die();
        }
        if ($stmt->affected_rows === 1) {
            $inserted_count++;
        } elseif ($stmt->affected_rows === 2) {
            $updated_count++;
        }
        $stmt->close();
    }

    // Update linux_updates table with the total number of installed and available updates
    $total_pending_updates = count($parsed_pending_updates);
    $total_installed_updates = count($parsed_installed_updates);

    $stmt = $conn->prepare("INSERT INTO linux_updates (hostname, pending_updates, installed_updates, project_id, task_id) VALUES (?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE pending_updates=VALUES(pending_updates), installed_updates=VALUES(installed_updates), project_id=VALUES(project_id), task_id=VALUES(task_id), timestamp=CURRENT_TIMESTAMP");
    if ($stmt === false) {
        $response['message'] = "Prepare failed: " . $conn->error;
        echo json_encode($response);
        die();
    }
    $stmt->bind_param("siiii", $hostname, $total_pending_updates, $total_installed_updates, $project_id, $task_id);
    if (!$stmt->execute()) {
        $response['message'] = "Execute failed: " . $stmt->error;
        echo json_encode($response);
        die();
    }
    $stmt->close();

    $response = [
        'status' => 'success',
        'message' => 'Updates stored successfully',
        'inserted' => $inserted_count,
        'updated' => $updated_count
    ];
} else {
    $response['message'] = 'Invalid data: Missing required fields';
}

// Set header to JSON
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>