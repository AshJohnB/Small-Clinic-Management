<?php
/**
 * patients/get_visits.php
 * Returns JSON array of visits for a patient.
 */
session_start();
include '../config/connection.php';

$patient_id = isset($_GET['patient_id']) ? (int) $_GET['patient_id'] : 0;

$sql  = "SELECT id, diagnosis, doctor_notes, visit_date
         FROM visits
         WHERE patient_id = ?
         ORDER BY visit_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$visits = [];
while ($row = $result->fetch_assoc()) {
    $visits[] = $row;
}
$stmt->close();

header('Content-Type: application/json');
echo json_encode($visits);
