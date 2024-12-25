<?php
/**
 * patients/add_visit.php
 * Endpoint that inserts a visit record (AJAX).
 */
session_start();
include '../config/connection.php';  // or '../includes/connection.php' if you keep it there

$patient_id   = isset($_POST['patient_id']) ? (int) $_POST['patient_id'] : 0;
$visit_date   = $_POST['visit_date'] ?? '';
$diagnosis    = trim($_POST['diagnosis'] ?? '');
$doctor_notes = trim($_POST['doctor_notes'] ?? '');

$errors = [];

// Validate visit_date
if ($visit_date === '') {
    $errors[] = "Visit date is required.";
} elseif ($visit_date > date('Y-m-d')) {
    $errors[] = "Visit date cannot be in the future.";
}

// Validate diagnosis
if (empty($diagnosis)) {
    $errors[] = "Diagnosis is required.";
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)]);
    exit;
}

$sql  = "INSERT INTO visits (patient_id, diagnosis, doctor_notes, visit_date)
         VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isss', $patient_id, $diagnosis, $doctor_notes, $visit_date);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Visit added successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error adding visit: ' . $stmt->error]);
}
$stmt->close();
