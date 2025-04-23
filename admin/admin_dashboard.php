<?php
session_start();
include '../config.php';


if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}


$voters_query = "SELECT COUNT(*) as total FROM voters";
$elections_query = "SELECT COUNT(*) as total FROM elections";
$candidates_query = "SELECT COUNT(*) as total FROM candidates";

$voters_result = mysqli_query($conn, $voters_query);
$elections_result = mysqli_query($conn, $elections_query);
$candidates_result = mysqli_query($conn, $candidates_query);

$total_voters = mysqli_fetch_assoc($voters_result)['total'];
$total_elections = mysqli_fetch_assoc($elections_result)['total'];
$total_candidates = mysqli_fetch_assoc($candidates_result)['total'];


$current_date = date('Y-m-d');
$current_time = date('H:i:s');
$active_elections_query = "SELECT * FROM elections 
                          WHERE (start_date < '$current_date' OR (start_date = '$current_date' AND start_time <= '$current_time'))
                          AND (end_date > '$current_date' OR (end_date = '$current_date' AND end_time > '$current_time'))";
$active_elections_result = mysqli_query($conn, $active_elections_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../combined.css">
</head>
<body>
    <header>
        <a href="admin_dashboard.php" class="logo">Voting System</a>
        <div class="nav-links">
            <a href="manage_voters.php">Register</a>
            <a href="manage_elections.php">Manage Elections</a>
            <a href="manage_candidates.php">Manage Candidates</a>
            <a href="view_results.php">View Results</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header> 

    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Voters</h3>
                <p><?php echo $total_voters; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Elections</h3>
                <p><?php echo $total_elections; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Candidates</h3>
                <p><?php echo $total_candidates; ?></p>
            </div>
        </div>

    </div>
</body>
</html>
