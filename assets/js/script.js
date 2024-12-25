// assets/js/script.js

$(document).ready(function() {
    let currentPatientId = null;

    // ===================== VIEW PATIENT DETAILS ===================== //
    $(document).on('click', '.viewBtn', function() {
        const row = $(this).closest('tr');
        $('#viewFirstname').text(row.find('td:eq(1)').text());
        $('#viewLastname').text(row.find('td:eq(2)').text());
        $('#viewAge').text(row.find('td:eq(3)').text());
        $('#viewGender').text(row.find('td:eq(4)').text());
        $('#viewAddress').text(row.find('td:eq(5)').text());
        $('#viewContact').text(row.find('td:eq(6)').text());
        $('#viewModal').modal('show');
    });

    // ===================== VIEW VISITS ===================== //
    $(document).on('click', '.viewVisitsBtn', function() {
        const row = $(this).closest('tr');
        currentPatientId = row.data('patient-id');
        loadVisits(currentPatientId);
    });

    // ===================== ADD VISIT ===================== //
    $('.addVisitBtn').on('click', function() {
        $('#addVisitPatientId').val(currentPatientId);
        $('#addVisitForm')[0].reset();
        $('#addVisitModal').modal('show');
    });

    $('#addVisitForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'add_visit.php',     // if your file is in the same folder as view.php
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success');
                    $('#addVisitModal').modal('hide');
                    loadVisits(currentPatientId);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });

    // ===================== EDIT VISIT ===================== //
    $(document).on('click', '.editVisitBtn', function() {
        const visitId = $(this).data('id');
        $.ajax({
            url: 'get_visit_details.php',
            method: 'GET',
            data: { visit_id: visitId },
            dataType: 'json',
            success: function(data) {
                if (data) {
                    $('#editVisitId').val(data.id);
                    $('#editVisitDate').val(data.visit_date);
                    $('#editVisitDiagnosis').val(data.diagnosis);
                    $('#editVisitNotes').val(data.doctor_notes);
                    $('#editVisitModal').modal('show');
                } else {
                    Swal.fire('Error', 'Visit details not found', 'error');
                }
            }
        });
    });

    $('#editVisitForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'update_visit.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success');
                    $('#editVisitModal').modal('hide');
                    loadVisits(currentPatientId);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });

    // ===================== DELETE VISIT ===================== //
    $(document).on('click', '.deleteVisitBtn', function() {
        const visitId = $(this).data('id');
        Swal.fire({
            icon: 'warning',
            title: 'Delete Visit?',
            text: 'This action cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_visit.php',
                    method: 'POST',
                    data: { visit_id: visitId },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Deleted', res.message, 'success');
                            loadVisits(currentPatientId);
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // ===================== DELETE PATIENT ===================== //
    $(document).on('click', '.deletePatientBtn', function() {
        const patientId = $(this).data('id');

        Swal.fire({
            icon: 'warning',
            title: 'Delete Patient?',
            text: 'Are you sure you want to delete this patient? This action cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete.php',  // The file that performs the patient delete
                    method: 'POST',
                    data: { id: patientId },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Deleted', res.message, 'success');
                            // Remove the row from the table or reload the page:
                            $(`tr[data-patient-id="${patientId}"]`).remove();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'An error occurred while deleting the patient.', 'error');
                    }
                });
            }
        });
    });
});

/**
 * loadVisits(patientId)
 * Fetches visits for a specific patient, displays them, then shows the #visitsModal.
 */
function loadVisits(patientId) {
    $.ajax({
        url: 'get_visits.php',
        method: 'GET',
        data: { patient_id: patientId },
        dataType: 'json',
        success: function(data) {
            $('#visitsRecords').empty();
            if (data.length > 0) {
                data.forEach(visit => {
                    $('#visitsRecords').append(`
                        <tr>
                            <td>${visit.visit_date}</td>
                            <td>${visit.diagnosis}</td>
                            <td>${visit.doctor_notes || ''}</td>
                            <td>
                                <button 
                                    class="btn btn-warning btn-sm editVisitBtn" 
                                    data-id="${visit.id}"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button 
                                    class="btn btn-danger btn-sm deleteVisitBtn" 
                                    data-id="${visit.id}"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                $('#visitsRecords').append('<tr><td colspan="4">No visits found.</td></tr>');
            }
            $('#visitsModal').modal('show');
        },
        error: function() {
            $('#visitsRecords').empty().append('<tr><td colspan="4">Error loading visits.</td></tr>');
            $('#visitsModal').modal('show');
        }
    });
}
