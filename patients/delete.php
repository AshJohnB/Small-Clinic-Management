<?php
/**
 * patients/delete.php
 * Deletes a patient by ID (received via POST), returns JSON.
 */
session_start();
include '../config/connection.php';

// 1) Get patient ID from POST
$patient_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// 2) Prepare the delete statement
$sql  = "DELETE FROM patients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $patient_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Patient record deleted successfully.'
        ]);
    } else {
        // No row found with that ID
        echo json_encode([
            'status'  => 'error',
            'message' => 'Patient not found.'
        ]);
    }
} else {
    // Query error
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error deleting patient: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
