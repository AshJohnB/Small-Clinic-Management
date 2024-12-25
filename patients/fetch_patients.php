<?php
/**
 * patients/fetch_patients.php
 * Example: returns table rows (not JSON).
 */
include '../config/connection.php';

$page  = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query  = "SELECT * FROM patients LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['firstname']}</td>
        <td>{$row['lastname']}</td>
        <td>{$row['age']}</td>
        <td>{$row['gender']}</td>
        <td>{$row['address']}</td>
        <td>{$row['contact']}</td>
        <td>{$row['admission_date']}</td>
        <td>
            <button class='btn btn-info btn-sm'><i class='fas fa-eye'></i> View</button>
            <a href='update.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Update</a>
            <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i> Delete</a>
        </td>
    </tr>";
}
