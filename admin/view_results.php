<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get all elections for the dropdown
$elections_query = "SELECT * FROM elections ORDER BY start_date DESC";
$elections_result = mysqli_query($conn, $elections_query);

// Get selected election ID from URL or form
$selected_election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';

// Get results for selected election
$results = [];
$total_votes = 0;
$election_title = '';

if (!empty($selected_election_id)) {
    // Get election details
    $election_query = "SELECT * FROM elections WHERE id = $selected_election_id";
    $election_result = mysqli_query($conn, $election_query);
    $election = mysqli_fetch_assoc($election_result);
    $election_title = $election['title'];

    // Get candidates and their vote counts
    $results_query = "SELECT c.*, COUNT(v.id) as vote_count 
                     FROM candidates c 
                     LEFT JOIN votes v ON c.id = v.candidate_id 
                     WHERE c.election_id = $selected_election_id 
                     GROUP BY c.id 
                     ORDER BY vote_count DESC";
    $results_result = mysqli_query($conn, $results_query);

    while ($row = mysqli_fetch_assoc($results_result)) {
        $results[] = $row;
        $total_votes += $row['vote_count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results - Voting System</title>
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
        <div class="dashboard-header">
            <h1>Election Results</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="GET" class="form-inline">
                <div class="form-group">
                    <label for="election_id">Select Election:</label>
                    <select name="election_id" id="election_id" class="form-control" required>
                        <option value="">Select Election</option>
                        <?php while ($election = mysqli_fetch_assoc($elections_result)): ?>
                            <option value="<?php echo $election['id']; ?>" <?php echo (isset($_GET['election_id']) && $_GET['election_id'] == $election['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($election['title']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">View Results</button>
            </form>
        </div>

        <?php if (!empty($selected_election_id)): ?>
            <div class="results-container">
                <h2><?php echo htmlspecialchars($election_title); ?></h2>
                <div class="card">
                    <h3>Election Summary</h3>
                    <p class="total-votes">Total Votes Cast: <strong><?php echo $total_votes; ?></strong></p>
                </div>

                <div class="results-chart">
                    <?php foreach ($results as $result): ?>
                        <div class="chart-bar">
                            <div class="chart-label">
                                <?php echo htmlspecialchars($result['name']); ?>
                                (<?php echo htmlspecialchars($result['role']); ?>)
                            </div>
                            <div class="chart-value">
                                <div class="chart-fill" style="width: <?php echo $total_votes > 0 ? ($result['vote_count'] / $total_votes * 100) : 0; ?>%"></div>
                            </div>
                            <div class="chart-percentage">
                                <?php 
                                $percentage = $total_votes > 0 ? ($result['vote_count'] / $total_votes) * 100 : 0;
                                echo number_format($percentage, 2) . '%';
                                ?>
                                (<?php echo $result['vote_count']; ?> votes)
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Please select an election to view results.
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 