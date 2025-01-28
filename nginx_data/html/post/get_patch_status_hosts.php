<?php
$servername = "localhost";
$username = "semaphore_user";
$password = "DFyuqwhjty34JK@#23@#";
$dbname = "semaphore_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$project_id = $_GET['project_id'];

// Fetch hosts and number of available updates from patching_status and linux_updates tables
$query = "
    SELECT hostname, timestamp, found_update_count AS available_updates, 'windows' AS os_type
    FROM patching_status
    WHERE project_id = ?
    AND timestamp = (
        SELECT MAX(timestamp)
        FROM patching_status AS ps
        WHERE ps.hostname = patching_status.hostname
        AND ps.project_id = patching_status.project_id
    )
    UNION
    SELECT hostname, timestamp, pending_updates AS available_updates, 'linux' AS os_type
    FROM linux_updates
    WHERE project_id = ?
    AND timestamp = (
        SELECT MAX(timestamp)
        FROM linux_updates AS lu
        WHERE lu.hostname = linux_updates.hostname
        AND lu.project_id = linux_updates.project_id
    )
    ORDER BY hostname";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $project_id, $project_id);
$stmt->execute();
$result = $stmt->get_result();

$hosts = [];
while ($row = $result->fetch_assoc()) {
    $row['formatted_timestamp'] = formatTimestamp($row['timestamp']);
    $hostname = $row['hostname'];

    if ($row['os_type'] === 'linux') {
        // Fetch pending and installed updates for the Linux host
        $updates_query = "
            SELECT pending_updates, installed_updates
            FROM linux_updates
            WHERE hostname = ?
            AND project_id = ?
            ORDER BY timestamp DESC
            LIMIT 1";
        $updates_stmt = $conn->prepare($updates_query);
        $updates_stmt->bind_param("si", $hostname, $project_id);
        $updates_stmt->execute();
        $updates_result = $updates_stmt->get_result();
        if ($updates_row = $updates_result->fetch_assoc()) {
            $row['available_updates'] = $updates_row['pending_updates'];
            $row['installed_updates'] = $updates_row['installed_updates'];
        } else {
            $row['available_updates'] = 0;
            $row['installed_updates'] = 0;
        }
        $updates_stmt->close();
    }

    $hosts[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($hosts);

function formatTimestamp($timestamp) {
    // Set the desired timezone
//    $timezone = new DateTimeZone('America/Chicago'); // Change to your desired timezone
    $timezone = new DateTimeZone('America/New_York'); 

    $datetime1 = new DateTime($timestamp, $timezone);
    $datetime2 = new DateTime('now', $timezone);
    $interval = $datetime1->diff($datetime2);

    if ($interval->y > 0) {
        return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
    } elseif ($interval->m > 0) {
        return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
    } elseif ($interval->d > 0) {
        return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
    } elseif ($interval->h > 0) {
        return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
    } elseif ($interval->i > 0) {
        return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'just now';
    }
}
?>
