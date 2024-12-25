<?php
include 'includes/connection.php';

$sql = "SELECT id, password FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hashed_password = password_hash($row['password'], PASSWORD_DEFAULT);
        $id = $row['id'];

        // Update the password with its hashed version
        $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = $id";
        $conn->query($update_sql);
    }
    echo "Passwords updated successfully!";
} else {
    echo "No users found.";
}

$conn->close();
?>
