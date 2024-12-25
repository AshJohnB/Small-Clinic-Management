<?php
/**
 * patients/update.php
 * A page to update an existing patient's info.
 */
session_start();
include '../config/connection.php';

// Fetch existing data
if (isset($_GET['id'])) {
    $id      = (int) $_GET['id'];
    $result  = $conn->query("SELECT * FROM patients WHERE id = $id");
    $row     = $result->fetch_assoc();
}

// Process update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id             = (int) $_POST['id'];
    $firstname      = $_POST['firstname'];
    $lastname       = $_POST['lastname'];
    $age            = (int) $_POST['age'];
    $gender         = $_POST['gender'];
    $address        = $_POST['address'];
    $contact        = $_POST['contact'];
    $admission_date = $_POST['admission_date'];

    $sql = "UPDATE patients
            SET firstname='$firstname',
                lastname='$lastname',
                age=$age,
                gender='$gender',
                address='$address',
                contact='$contact',
                admission_date='$admission_date'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Patient record updated successfully!'); window.location.href='view.php';</script>";
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Include header
include '../templates/header.php';
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <h4>Update Patient</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $row['id'] ?? 0; ?>">

                <div class="form-group mb-3">
                    <label>First Name</label>
                    <input
                        type="text"
                        name="firstname"
                        value="<?php echo $row['firstname'] ?? ''; ?>"
                        class="form-control"
                        required
                    >
                </div>
                <div class="form-group mb-3">
                    <label>Last Name</label>
                    <input
                        type="text"
                        name="lastname"
                        value="<?php echo $row['lastname'] ?? ''; ?>"
                        class="form-control"
                        required
                    >
                </div>
                <div class="form-group mb-3">
                    <label>Age</label>
                    <input
                        type="number"
                        name="age"
                        value="<?php echo $row['age'] ?? 0; ?>"
                        class="form-control"
                        required
                    >
                </div>
                <div class="form-group mb-3">
                    <label>Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="Male"   <?php if(($row['gender'] ?? '') === 'Male')   echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if(($row['gender'] ?? '') === 'Female') echo 'selected'; ?>>Female</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>Address</label>
                    <input
                        type="text"
                        name="address"
                        value="<?php echo $row['address'] ?? ''; ?>"
                        class="form-control"
                        required
                    >
                </div>
                <div class="form-group mb-3">
                    <label>Contact</label>
                    <input
                        type="text"
                        name="contact"
                        value="<?php echo $row['contact'] ?? ''; ?>"
                        class="form-control"
                        required
                    >
                </div>
                <div class="form-group mb-3">
                    <label>Admission Date</label>
                    <input
                        type="date"
                        name="admission_date"
                        value="<?php echo $row['admission_date'] ?? date('Y-m-d'); ?>"
                        class="form-control"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-success">Update Patient</button>
                <a href="view.php" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
