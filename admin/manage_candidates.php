<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle candidate deletion
if (isset($_GET['delete'])) {
    $candidate_id = $_GET['delete'];
    $delete_query = "DELETE FROM candidates WHERE id = $candidate_id";
    mysqli_query($conn, $delete_query);
    header("Location: manage_candidates.php" . (isset($_GET['election_id']) ? "?election_id=" . $_GET['election_id'] : ""));
    exit();
}

// Get election ID from URL if specified
$election_id = isset($_GET['election_id']) ? $_GET['election_id'] : null;

// Get candidates based on election ID
if ($election_id) {
    $candidates_query = "SELECT c.*, e.title as election_title 
                        FROM candidates c 
                        JOIN elections e ON c.election_id = e.id 
                        WHERE c.election_id = $election_id 
                        ORDER BY c.created_at DESC";
} else {
    $candidates_query = "SELECT c.*, e.title as election_title 
                        FROM candidates c 
                        JOIN elections e ON c.election_id = e.id 
                        ORDER BY c.created_at DESC";
}

$candidates_result = mysqli_query($conn, $candidates_query);

// Get elections for dropdown
$elections_query = "SELECT * FROM elections ORDER BY start_date DESC";
$elections_result = mysqli_query($conn, $elections_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates - Voting System</title>
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
        <h1>Manage Candidates</h1>
        
       

        <!-- Candidates Table -->
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Batch</th>
                        <th>Role</th>
                        <th>Election</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($candidate = mysqli_fetch_assoc($candidates_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['batch']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['role']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['election_title']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($candidate['created_at'])); ?></td>
                            <td>
                                <div class="candidate-actions">
                                    <a href="edit_candidate.php?id=<?php echo $candidate['id']; ?>" class="btn btn-primary">Edit Candidate</a>
                                    <a href="manage_candidates.php?delete=<?php echo $candidate['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <a href="create_candidate.php" class="btn btn-primary">Add New Candidate</a>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html> 