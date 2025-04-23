<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Set the correct timezone
date_default_timezone_set('Asia/Karachi');

// Get candidate details
if (isset($_GET['id'])) {
    $candidate_id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM candidates WHERE id = $candidate_id";
    $result = mysqli_query($conn, $query);
    $candidate = mysqli_fetch_assoc($result);
    
    if (!$candidate) {
        $_SESSION['error'] = "Candidate not found";
        header("Location: manage_candidates.php");
        exit();
    }
} else {
    header("Location: manage_candidates.php");
    exit();
}

// Get all elections for dropdown
$current_date = date('Y-m-d H:i:s');
$elections_query = "SELECT * FROM elections 
                   WHERE CONCAT(end_date, ' ', end_time) > '$current_date' 
                   OR id = {$candidate['election_id']}
                   ORDER BY start_date DESC, start_time DESC";
$elections_result = mysqli_query($conn, $elections_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $batch = mysqli_real_escape_string($conn, $_POST['batch']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $election_id = mysqli_real_escape_string($conn, $_POST['election_id']);
    
    // Update candidate
    $update_query = "UPDATE candidates 
                    SET name = '$name', 
                        batch = '$batch',
                        role = '$role',
                        election_id = '$election_id'
                    WHERE id = $candidate_id";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success'] = "Candidate updated successfully";
        header("Location: manage_candidates.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating candidate: " . mysqli_error($conn);
        header("Location: edit_candidate.php?id=" . $candidate_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Candidate</title>
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
        <h1>Edit Candidate</h1>
        
       
        
        <div class="card">
            <form method="POST" class="form-group">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($candidate['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="batch">Batch:</label>
                    <input type="text" id="batch" name="batch" value="<?php echo htmlspecialchars($candidate['batch']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="">Select Role</option>
                        <?php
                        $roles = ['President', 'Vice President', 'Secretary', 'Treasurer', 'Member'];
                        foreach ($roles as $role_option):
                            $selected = ($candidate['role'] == $role_option) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $role_option; ?>" <?php echo $selected; ?>>
                                <?php echo $role_option; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="election_id">Election:</label>
                    <select id="election_id" name="election_id" required>
                        <option value="">Select Election</option>
                        <?php while ($election = mysqli_fetch_assoc($elections_result)): ?>
                            <option value="<?php echo $election['id']; ?>" 
                                    <?php echo ($candidate['election_id'] == $election['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($election['title']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Update Candidate</button>
                    <a href="manage_candidates.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 