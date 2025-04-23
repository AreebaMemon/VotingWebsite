<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Set the correct timezone
date_default_timezone_set('Asia/Karachi');

// Get all active elections for the dropdown
$current_date = date('Y-m-d H:i:s');
$elections_query = "SELECT * FROM elections 
                   WHERE CONCAT(end_date, ' ', end_time) > '$current_date' 
                   ORDER BY start_date DESC, start_time DESC";
$elections_result = mysqli_query($conn, $elections_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $batch = mysqli_real_escape_string($conn, $_POST['batch']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $election_id = mysqli_real_escape_string($conn, $_POST['election_id']);
    
    // Insert candidate
    $query = "INSERT INTO candidates (name, batch, role, election_id) 
              VALUES ('$name', '$batch', '$role', '$election_id')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Candidate added successfully";
    } else {
        $_SESSION['error'] = "Error adding candidate: " . mysqli_error($conn);
    }
    
    header("Location: manage_candidates.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Candidate</title>
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
        <h1>Add New Candidate</h1>
        
       
        <div class="card">
            <form method="POST" class="form-group">
                <div class="form-group">
                    <label for="election_id">Select Election:</label>
                    <select id="election_id" name="election_id" required>
                        <option value="">Select an election</option>
                        <?php while ($election = mysqli_fetch_assoc($elections_result)): ?>
                            <option value="<?php echo $election['id']; ?>">
                                <?php echo htmlspecialchars($election['title']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="name">Candidate Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="batch">Batch:</label>
                    <input type="text" id="batch" name="batch" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="">Select a role</option>
                        <option value="President">President</option>
                        <option value="Vice President">Vice President</option>
                        <option value="Secretary">Secretary</option>
                        <option value="Treasurer">Treasurer</option>
                        <option value="Member">Member</option>
                    </select>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Add Candidate</button>
                    <a href="manage_candidates.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>