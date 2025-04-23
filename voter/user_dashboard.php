<?php
session_start();
include '../config.php';

if (!isset($_SESSION['voter_id'])) {
    header("Location: ../login.php");
    exit();
}

// Set the correct timezone
date_default_timezone_set('Asia/Karachi');

$voter_id = $_SESSION['voter_id'];

// Get voter details
$voter_query = "SELECT * FROM voters WHERE id = $voter_id";
$voter_result = mysqli_query($conn, $voter_query);
$voter = mysqli_fetch_assoc($voter_result);

// Get active elections (current date is between start and end date)
$current_date = date('Y-m-d H:i:s');
$elections_query = "SELECT * FROM elections 
                   WHERE CONCAT(start_date, ' ', start_time) <= '$current_date' 
                   AND CONCAT(end_date, ' ', end_time) > '$current_date'
                   ORDER BY start_date DESC, start_time DESC";
$elections_result = mysqli_query($conn, $elections_query);

// Get upcoming elections (start date is after current date)
$upcoming_elections_query = "SELECT * FROM elections 
                              WHERE CONCAT(start_date, ' ', start_time) > '$current_date'
                              ORDER BY start_date ASC, start_time ASC";
$upcoming_elections_result = mysqli_query($conn, $upcoming_elections_query);

// Get past elections where voter has voted
$past_votes_query = "SELECT e.*, v.vote_date 
                    FROM elections e 
                    JOIN votes v ON e.id = v.election_id 
                    WHERE v.voter_id = $voter_id 
                    AND CONCAT(e.end_date, ' ', e.end_time) < '$current_date'
                    ORDER BY e.end_date DESC, e.end_time DESC";
$past_votes_result = mysqli_query($conn, $past_votes_query);

// Get all ended elections (regardless of whether the voter voted)
$ended_elections_query = "SELECT * FROM elections 
                         WHERE CONCAT(end_date, ' ', end_time) < '$current_date'
                         ORDER BY end_date DESC, end_time DESC";
$ended_elections_result = mysqli_query($conn, $ended_elections_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../combined.css">
</head>
<body>
    <header>
        <a href="user_dashboard.php" class="logo">Voting System</a>
        <div class="nav-links">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($voter['name']); ?>!</h1>
            <p>Here are the elections you can participate in.</p>
        </div>

      

        <!-- Active Elections -->
        <div class="card">
            <h2>Active Elections</h2>
            <?php if (mysqli_num_rows($elections_result) > 0): ?>
                <div class="election-grid">
                    <?php while ($election = mysqli_fetch_assoc($elections_result)): ?>
                        <?php
                        // Check if voter has already voted in this election
                        $vote_check_query = "SELECT * FROM votes WHERE election_id = {$election['id']} AND voter_id = $voter_id";
                        $vote_check_result = mysqli_query($conn, $vote_check_query);
                        $has_voted = mysqli_num_rows($vote_check_result) > 0;
                        ?>
                        <div class="election-card">
                            <h3><?php echo htmlspecialchars($election['title']); ?></h3>
                            <p><strong>Start Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['start_date'] . ' ' . $election['start_time'])); ?></p>
                            <p><strong>End Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['end_date'] . ' ' . $election['end_time'])); ?></p>
                            
                            <div class="election-actions">
                                <?php if ($has_voted): ?>
                                    <span class="badge badge-success">Voted</span>
                                <?php else: ?>
                                    <a href="cast_vote.php?election_id=<?php echo $election['id']; ?>" class="btn btn-primary">Vote Now</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No active elections at the moment. Please check back later.
                </div>
            <?php endif; ?>
        </div>

        <!-- Upcoming Elections -->
        <div class="card">
            <h2>Upcoming Elections</h2>
            <?php if (mysqli_num_rows($upcoming_elections_result) > 0): ?>
                <div class="election-grid">
                    <?php while ($election = mysqli_fetch_assoc($upcoming_elections_result)): ?>
                        <div class="election-card">
                            <h3><?php echo htmlspecialchars($election['title']); ?></h3>
                            <p><strong>Start Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['start_date'] . ' ' . $election['start_time'])); ?></p>
                            <p><strong>End Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['end_date'] . ' ' . $election['end_time'])); ?></p>
                            <div class="election-actions">
                                <span class="badge badge-warning">Upcoming</span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No upcoming elections at the moment. Please check back later.
                </div>
            <?php endif; ?>
        </div>

        <!-- Ended Elections -->
        <div class="card">
            <h2>Ended Elections</h2>
            <?php if (mysqli_num_rows($ended_elections_result) > 0): ?>
                <div class="election-grid">
                    <?php while ($election = mysqli_fetch_assoc($ended_elections_result)): 
                        // Check if voter has voted in this election
                        $vote_check_query = "SELECT * FROM votes WHERE election_id = {$election['id']} AND voter_id = $voter_id";
                        $vote_check_result = mysqli_query($conn, $vote_check_query);
                        $has_voted = mysqli_num_rows($vote_check_result) > 0;
                    ?>
                        <div class="election-card">
                            <h3><?php echo htmlspecialchars($election['title']); ?></h3>
                            <p><strong>Start Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['start_date'] . ' ' . $election['start_time'])); ?></p>
                            <p><strong>End Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['end_date'] . ' ' . $election['end_time'])); ?></p>
                           
                            <div class="election-actions">
                                <?php if ($has_voted): ?>
                                    <span class="badge badge-success">Voted</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Did Not Vote</span>
                                <?php endif; ?>
                            </div>
                        </div>
                            <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No ended elections at the moment.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 
