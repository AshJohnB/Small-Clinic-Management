<?php
/**
 * patients/update_visit.php
 * Updates a visit via AJAX.
 */
session_start();
include '../config/connection.php';

// Basic input sanitization
$visit_id     = isset($_POST['visit_id']) ? (int)$_POST['visit_id'] : 0;
$visit_date   = $_POST['visit_date'] ?? '';
$diagnosis    = trim($_POST['diagnosis'] ?? '');
$doctor_notes = trim($_POST['doctor_notes'] ?? '');

// Validation checks
$errors = [];

if ($visit_date === '') {
    $errors[] = "Visit date is required.";
} elseif ($visit_date > date('Y-m-d')) {
    $errors[] = "Visit date cannot be in the future.";
}

if (empty($diagnosis)) {
    $errors[] = "Diagnosis is required.";
}

// If we have errors, return them
if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)]);
    exit;
}

// 1) Check if the visit actually exists
$checkSql = "SELECT id FROM visits WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param('i', $visit_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    // The row doesn't exist => no update possible
    echo json_encode(['status' => 'error', 'message' => 'Visit record not found.']);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// 2) Update the visit
$sql  = "UPDATE visits 
         SET visit_date = ?, diagnosis = ?, doctor_notes = ?
         WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssi', $visit_date, $diagnosis, $doctor_notes, $visit_id);

if ($stmt->execute()) {
    // If query ran successfully, we consider it a success 
    // (even if no rows were "changed" because data was the same)
    echo json_encode(['status' => 'success', 'message' => 'Visit updated successfully.']);
} else {
    // If there's an actual MySQL error
    echo json_encode(['status' => 'error', 'message' => 'Error updating visit: '.$stmt->error]);
}

$stmt->close();
$conn->close();
