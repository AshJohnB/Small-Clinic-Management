<?php
/**
 * patients/view.php
 * Displays the list of patients with pagination, search, modals, etc.
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../public/login.php");
    exit();
}

include '../config/connection.php';

// Handle Pagination + Search
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$limit  = 10;
$offset = ($page - 1) * $limit;

$whereClause = '';
$params     = [];
$types      = '';

if ($search !== '') {
    $whereClause = "WHERE (firstname LIKE ? OR lastname LIKE ? OR address LIKE ? OR contact LIKE ?)";
    $searchTerm  = "%{$conn->real_escape_string($search)}%";
    $params      = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
    $types       = 'ssss';
}

// Count total records
$countSql = "SELECT COUNT(*) AS total FROM patients $whereClause";
$countStmt = $conn->prepare($countSql);
if ($whereClause !== '') {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countResult  = $countStmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages   = ceil($totalRecords / $limit);
$countStmt->close();

// Fetch paginated records
$dataSql = "SELECT * FROM patients $whereClause ORDER BY id ASC LIMIT ? OFFSET ?";
$dataStmt = $conn->prepare($dataSql);

if ($whereClause !== '') {
    $types .= 'ii';
    $params[] = $limit;
    $params[] = $offset;
    $dataStmt->bind_param($types, ...$params);
} else {
    $dataStmt->bind_param('ii', $limit, $offset);
}
$dataStmt->execute();
$result = $dataStmt->get_result();

// Include header
include '../templates/header.php';
?>

<!-- Optional: Show session-based SweetAlert -->
<?php if (isset($_SESSION['alert_type']) && isset($_SESSION['alert_message'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: '<?php echo $_SESSION['alert_type']; ?>',
        title: '<?php echo $_SESSION['alert_type'] === 'success' ? 'Success!' : 'Error!'; ?>',
        text: '<?php echo addslashes($_SESSION['alert_message']); ?>',
        showConfirmButton: false,
        timer: 2000
    });
});
</script>
<?php 
    unset($_SESSION['alert_type'], $_SESSION['alert_message']);
endif;
?>

<div class="container mt-4 mb-5">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex flex-wrap justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-users"></i> Patient List</h4>
            <div class="d-flex flex-wrap align-items-center">
                <!-- Search Form -->
                <form class="d-flex me-2" method="GET" action="">
                    <input type="hidden" name="page" value="1">
                    <input 
                        class="form-control form-control-sm me-2" 
                        type="text" 
                        name="search" 
                        placeholder="Search patients..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                    <button class="btn btn-light btn-sm text-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <!-- Add Patient Button -->
                <button 
                    class="btn btn-light text-primary btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#addModal"
                >
                    <i class="fas fa-plus"></i> Add Patient
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center mb-0" id="patientTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patientRecords">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr data-patient-id="<?php echo $row['id']; ?>">
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['firstname']; ?></td>
                                    <td><?php echo $row['lastname']; ?></td>
                                    <td><?php echo $row['age']; ?></td>
                                    <td><?php echo $row['gender']; ?></td>
                                    <td><?php echo $row['address']; ?></td>
                                    <td><?php echo $row['contact']; ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm viewBtn">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn btn-secondary btn-sm viewVisitsBtn">
                                            <i class="fas fa-stethoscope"></i> Visits
                                        </button>
                                        <a 
                                            href="update.php?id=<?php echo $row['id']; ?>" 
                                            class="btn btn-warning btn-sm"
                                        >
                                            <i class="fas fa-edit"></i> Update
                                        </a>
                                        <!-- Instead of a direct link, use a button for AJAX delete -->
                                        <button 
                                            class="btn btn-danger btn-sm deletePatientBtn" 
                                            data-id="<?php echo $row['id']; ?>"
                                        >
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8">No records found</td></tr>
                        <?php endif; ?>
                        <?php $dataStmt->close(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="card-footer d-flex justify-content-center">
            <nav>
                <ul class="pagination pagination-sm">
                    <?php
                        $baseUrl  = "?search=" . urlencode($search) . "&page=";
                        $prevPage = $page > 1 ? $page - 1 : 1; 
                    ?>
                    <li class="page-item <?php if ($page == 1) echo 'disabled'; ?>">
                        <a 
                            class="page-link" 
                            href="<?php echo $baseUrl . $prevPage; ?>" 
                            aria-label="Previous"
                        >
                            &laquo;
                        </a>
                    </li>

                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?php if ($p == $page) echo 'active'; ?>">
                            <a class="page-link" href="<?php echo $baseUrl . $p; ?>">
                                <?php echo $p; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php
                        $nextPage = $page < $totalPages ? $page + 1 : $totalPages; 
                    ?>
                    <li class="page-item <?php if ($page == $totalPages) echo 'disabled'; ?>">
                        <a 
                            class="page-link" 
                            href="<?php echo $baseUrl . $nextPage; ?>" 
                            aria-label="Next"
                        >
                            &raquo;
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ============= Add Patient Modal ============= -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addModalLabel">Add New Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="add.php" method="POST" id="addPatientForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>First Name</label>
                        <input 
                            type="text" 
                            name="firstname" 
                            class="form-control" 
                            required
                        >
                    </div>
                    <div class="form-group mb-3">
                        <label>Last Name</label>
                        <input 
                            type="text" 
                            name="lastname" 
                            class="form-control" 
                            required
                        >
                    </div>
                    <div class="form-group mb-3">
                        <label>Age</label>
                        <input 
                            type="number" 
                            name="age" 
                            class="form-control" 
                            min="1" 
                            required
                        >
                    </div>
                    <div class="form-group mb-3">
                        <label>Gender</label>
                        <select name="gender" class="form-control" required>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Address</label>
                        <input 
                            type="text" 
                            name="address" 
                            class="form-control" 
                            required
                        >
                    </div>
                    <div class="form-group mb-3">
                        <label>Contact</label>
                        <input 
                            type="text" 
                            name="contact" 
                            class="form-control" 
                            pattern="^09\d{9}$" 
                            title="Please enter an 11-digit number starting with '09' (e.g. 09123456789)"
                            required
                        >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Patient</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============= View Patient Modal ============= -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewModalLabel">Patient Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>First Name:</strong> <span id="viewFirstname"></span></p>
                <p><strong>Last Name:</strong> <span id="viewLastname"></span></p>
                <p><strong>Age:</strong> <span id="viewAge"></span></p>
                <p><strong>Gender:</strong> <span id="viewGender"></span></p>
                <p><strong>Address:</strong> <span id="viewAddress"></span></p>
                <p><strong>Contact:</strong> <span id="viewContact"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- ============= Visits Modal ============= -->
<div class="modal fade" id="visitsModal" tabindex="-1" aria-labelledby="visitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="visitsModalLabel">Patient Visits</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Add Visit Button -->
                <button class="btn btn-success btn-sm mb-3 addVisitBtn">
                    <i class="fas fa-plus"></i> Add Visit
                </button>
                <table class="table table-striped table-hover text-center">
                    <thead>
                        <tr>
                            <th>Visit Date</th>
                            <th>Diagnosis</th>
                            <th>Doctor's Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="visitsRecords">
                        <tr><td colspan="4">No visits found.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ============= Add Visit Modal ============= -->
<div class="modal fade" id="addVisitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Visit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addVisitForm">
                <div class="modal-body">
                    <input type="hidden" name="patient_id" id="addVisitPatientId">
                    <div class="form-group mb-3">
                        <label>Visit Date</label>
                        <input type="date" name="visit_date" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Diagnosis</label>
                        <textarea name="diagnosis" class="form-control" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Doctor's Notes</label>
                        <textarea name="doctor_notes" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Visit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============= Update Visit Modal ============= -->
<div class="modal fade" id="editVisitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Update Visit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editVisitForm">
                <div class="modal-body">
                    <input type="hidden" name="visit_id" id="editVisitId">
                    <div class="form-group mb-3">
                        <label>Visit Date</label>
                        <input 
                            type="date" 
                            name="visit_date" 
                            id="editVisitDate" 
                            class="form-control" 
                            required
                        >
                    </div>
                    <div class="form-group mb-3">
                        <label>Diagnosis</label>
                        <textarea 
                            name="diagnosis" 
                            id="editVisitDiagnosis" 
                            class="form-control" 
                            required
                        ></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Doctor's Notes</label>
                        <textarea 
                            name="doctor_notes" 
                            id="editVisitNotes" 
                            class="form-control"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update Visit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
