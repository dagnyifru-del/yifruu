<?php
// Database connection
$servername = "localhost";
$username   = "root";   // default XAMPP user
$password   = "123456";       // default XAMPP password is empty
$dbname     = "portfolio_db";

// Connect
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data safely
$fname   = htmlspecialchars($_POST['fname']);
$lname   = htmlspecialchars($_POST['lname']);
$email   = htmlspecialchars($_POST['email']);
$comment = htmlspecialchars($_POST['comment']);

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Existing user → save comment
    $row = $result->fetch_assoc();
    $user_id = $row['id'];

    $stmt = $conn->prepare("INSERT INTO comments (user_id, comment) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $comment);
    $stmt->execute();

    echo "Thank you, $fname! Your comment has been saved.";
} else {
    // New user → create account first
    $stmt = $conn->prepare("INSERT INTO users (fname, lname, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fname, $lname, $email);
    $stmt->execute();
    $user_id = $stmt->insert_id;

    // Save comment
    $stmt = $conn->prepare("INSERT INTO comments (user_id, comment) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $comment);
    $stmt->execute();

    echo "Account created for $fname! Your comment has been saved.";
}

$stmt->close();
$conn->close();
?>