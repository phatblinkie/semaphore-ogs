<?php
$servername = "localhost";
$username = "semaphore_user";
$password = "DFyuqwhjty34JK@#23@#";
$dbname = "semaphore_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$project_id = $_GET['project_id'];
$hostname = $_GET['hostname'];
$os_type = $_GET['os_type'];

$hostDetails = [];

if ($os_type === 'windows') {
    // Fetch the most recent data for the specified host from patching_status table
    $query = "
        SELECT *
        FROM patching_status
        WHERE project_id = ?
        AND hostname = ?
        ORDER BY timestamp DESC
        LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $project_id, $hostname);
    $stmt->execute();
    $result = $stmt->get_result();
    $hostDetails = $result->fetch_assoc();

    if ($hostDetails) {
        // Fetch the list of updates for the specified host
        $query = "
            SELECT title
            FROM patching_updates
            WHERE patching_status_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $hostDetails['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $updates = [];
        while ($row = $result->fetch_assoc()) {
            $updates[] = $row['title'];
        }
        $hostDetails['updates'] = $updates;

        // Fetch the list of installed updates for the specified host
        $query = "
            SELECT title
            FROM windows_update_history
            WHERE hostname = ?
              AND operation = 'Installation'
              AND status = 'Succeeded'
            ORDER BY date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $hostname);
        $stmt->execute();
        $result = $stmt->get_result();
        $installedUpdates = [];
        while ($row = $result->fetch_assoc()) {
            $installedUpdates[] = $row['title'];
        }
        $hostDetails['installedUpdates'] = $installedUpdates;

	        // Fetch the list of failed updates for the specified host
        $query = "
            SELECT title, status, date
            FROM windows_update_history
            WHERE hostname = ?
            AND operation = 'Installation'
            AND status != 'Succeeded'
            ORDER BY date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $hostname);
        $stmt->execute();
        $result = $stmt->get_result();
        $failedUpdates = [];
        while ($row = $result->fetch_assoc()) {
            $failedUpdates[] = [
            'title' => $row['title'],
	    'status' => $row['status'],
	    'date' => $row['date']
            ];
        }
        $hostDetails['Failures'] = $failedUpdates;

    }
} elseif ($os_type === 'linux') {
    // Fetch the most recent data for the specified host from linux_updates table
    $query = "
        SELECT *
        FROM linux_updates
        WHERE project_id = ?
        AND hostname = ?
        ORDER BY timestamp DESC
        LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $project_id, $hostname);
    $stmt->execute();
    $result = $stmt->get_result();
    $hostDetails = $result->fetch_assoc();

    if ($hostDetails) {
        // Fetch the list of pending updates for the specified host
        $hostDetails['updates'] = [];
        $pending_updates_query = "
            SELECT update_name, version, repo
            FROM linux_pending_updates
            WHERE hostname = ?
            AND project_id = ?";
        $pending_updates_stmt = $conn->prepare($pending_updates_query);
        $pending_updates_stmt->bind_param("si", $hostname, $project_id);
        $pending_updates_stmt->execute();
        $pending_updates_result = $pending_updates_stmt->get_result();
        while ($row = $pending_updates_result->fetch_assoc()) {
            $hostDetails['updates'][] = [
                'name' => $row['update_name'],
                'version' => $row['version'],
                'repo' => $row['repo']
            ];
        }
        $pending_updates_stmt->close();

        // Fetch the list of installed updates for the specified host
        $hostDetails['installedUpdates'] = [];
        $installed_updates_query = "
            SELECT update_name, version, repo
            FROM linux_installed_updates
            WHERE hostname = ?
            AND project_id = ?";
        $installed_updates_stmt = $conn->prepare($installed_updates_query);
        $installed_updates_stmt->bind_param("si", $hostname, $project_id);
        $installed_updates_stmt->execute();
        $installed_updates_result = $installed_updates_stmt->get_result();
        while ($row = $installed_updates_result->fetch_assoc()) {
            $hostDetails['installedUpdates'][] = [
                'name' => $row['update_name'],
                'version' => $row['version'],
                'repo' => $row['repo']
            ];
        }
        $installed_updates_stmt->close();
    }
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($hostDetails);
?>
