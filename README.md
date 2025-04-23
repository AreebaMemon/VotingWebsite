Election Management System
A comprehensive web-based application for managing elections, candidates, and voters with secure voting functionality.

Features
Admin Features
📊 Dashboard with election statistics
🗳️ Create and manage elections
👥 Manage candidates and voters
📈 View election results
🔒 Secure admin authentication
Voter Features
👤 User-friendly voter dashboard
🗳️ Cast votes in active elections
📅 View upcoming and past elections
🔐 Secure voter authentication
Tech Stack
Frontend
HTML5
CSS3 (with responsive design)
JavaScript
Bootstrap/Poppins for styling
Backend
PHP
MySQL Database
Apache Server
Installation
Prerequisites

XAMPP/WAMP/LAMP server
PHP 7.4 or higher
MySQL 5.7 or higher
Web browser (Chrome, Firefox, Safari)
Setup

# Clone the repository
git clone [repository-url]

# Move files to your web server directory
# (e.g., for XAMPP: C:/xampp/htdocs/vote)

# Import the database
- Open phpMyAdmin
- Create a new database named 'vote'
- Import the voting_system.sql file
Configuration

Update database credentials in config.php
Set proper timezone in PHP configuration
Ensure proper file permissions
Project Structure
vote/
├── admin/
│   ├── admin_dashboard.php
│   ├── manage_elections.php
│   ├── manage_candidates.php
│   ├── manage_voters.php
│   ├── view_results.php
│   └── create_election.php
├── voter/
│   ├── user_dashboard.php
│   ├── cast_vote.php
│   └── view_history.php
├── config.php
├── regsiter.php
├── logout.php
├── index.php
└── combined.css
Database Schema
Tables
admins

id, username, password, created_at
voters

id, name, username, password, email, dob, created_at
elections

id, title, description, start_date, start_time, end_date, end_time, created_at
candidates

id, name, role, batch, election_id, created_at
votes

id, voter_id, election_id, candidate_id, vote_date
Usage
Admin Access

Login with admin credentials
Manage elections, candidates, and voters
View election results
Voter Access

Register as a voter
Login to view active elections
Cast votes in active elections
Security Features
🔐 Password hashing
🔒 Session-based authentication
🛡️ Input validation
🚫 SQL injection prevention
🛡️ XSS protection
Responsive Design
📱 Mobile-friendly interface
💻 Desktop optimized
📊 Responsive tables and forms
🎨 Consistent styling across devices
