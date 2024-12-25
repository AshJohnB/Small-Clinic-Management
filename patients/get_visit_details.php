<?php
/**
 * patients/get_visit_details.php
 * Returns JSON for a specific visit_id.
 */
session_start();
include '../config/connection.php';

$visit_id = isset($_GET['visit_id']) ? (int) $_GET['visit_id'] : 0;

$sql  = "SELECT id, diagnosis, doctor_notes, visit_date FROM visits WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $visit_id);
$stmt->execute();
$result = $stmt->get_result();

$visit = $result->fetch_assoc();

$stmt->close();

header('Content-Type: application/json');
echo json_encode($visit ? $visit : null);
