<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle voter deletion
if (isset($_GET['delete'])) {
    $voter_id = $_GET['delete'];
    $delete_query = "DELETE FROM voters WHERE id = $voter_id";
    mysqli_query($conn, $delete_query);
    header("Location: manage_voters.php");
    exit();
}

// Get all voters
$voters_query = "SELECT * FROM voters ORDER BY created_at DESC";
$voters_result = mysqli_query($conn, $voters_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voters - Voting System</title>
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
        <h1>Register</h1>
        
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Date of Birth</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($voter = mysqli_fetch_assoc($voters_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($voter['name']); ?></td>
                            <td><?php echo htmlspecialchars($voter['username']); ?></td>
                            <td><?php echo htmlspecialchars($voter['email']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($voter['dob'])); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($voter['created_at'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $voter['id']; ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this voter?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html> 