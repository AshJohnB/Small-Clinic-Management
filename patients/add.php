<?php
/**
 * patients/add.php
 * Processes POST data to add a new patient, then redirects back to view.php.
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../public/login.php");
    exit();
}

include '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname       = trim($_POST['firstname']);
    $lastname        = trim($_POST['lastname']);
    $age             = (int) $_POST['age'];
    $gender          = trim($_POST['gender']);
    $address         = trim($_POST['address']);
    $contact         = trim($_POST['contact']);
    // If you’re no longer collecting admission_date from the modal, omit it
    $admission_date  = date('Y-m-d'); // or use a hidden field if needed

    // Server-side validations
    $errors = [];
    if ($age <= 0) {
        $errors[] = "Age must be a positive number.";
    }
    if (!preg_match('/^09\d{9}$/', $contact)) {
        $errors[] = "Contact must be an 11-digit number starting with '09'.";
    }

    if (!empty($errors)) {
        $_SESSION['alert_type']    = 'error';
        $_SESSION['alert_message'] = implode(" ", $errors);
        header("Location: view.php");
        exit();
    }

    // Check for duplicates
    $checkSql  = "SELECT id FROM patients WHERE firstname = ? AND lastname = ? AND contact = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('sss', $firstname, $lastname, $contact);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['alert_type']    = 'error';
        $_SESSION['alert_message'] = "A patient with the same name and contact already exists.";
        $checkStmt->close();
        header("Location: view.php");
        exit();
    }
    $checkStmt->close();

    // Insert new patient
    $sql  = "INSERT INTO patients 
                (firstname, lastname, age, gender, address, contact, admission_date)
             VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssissss', $firstname, $lastname, $age, $gender, $address, $contact, $admission_date);

    if ($stmt->execute()) {
        $_SESSION['alert_type']    = 'success';
        $_SESSION['alert_message'] = "New patient added successfully.";
    } else {
        $_SESSION['alert_type']    = 'error';
        $_SESSION['alert_message'] = "Error adding patient: " . $stmt->error;
    }

    $stmt->close();
    header("Location: view.php");
    exit();
} else {
    // If GET request, just redirect to view
    header("Location: view.php");
    exit();
}
