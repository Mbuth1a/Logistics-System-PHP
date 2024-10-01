<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Co-Drivers - DANCO LTD Logistics System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/manage_co_driver.css">
</head>
<body>
<div class="sidebar">
    <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

    <div class="container mt-2 col-md-9">
        <h2><i class="fas fa-user-friends"></i> Co-Drivers</h2>
        <div class="table-container mt-2">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Employee Number</th>
                        <th scope="col">Co-Driver Name</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Email Address</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="codriverTableBody">
                    <!-- fetch details  -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Co-Driver Modal -->
    <div class="modal fade" id="editCoDriverModal" tabindex="-1" aria-labelledby="editCoDriverModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCoDriverModalLabel">Edit Co-Driver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editCoDriverForm">
                        <input type="hidden" name="id" id="editCoDriverId">
                        <div class="form-group">
                            <label for="edit_employeeNumber">Employee Number</label>
                            <input type="text" class="form-control" id="edit_employeeNumber" name="employee_number" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_coDriverName">Co-Driver Name</label>
                            <input type="text" class="form-control" id="edit_coDriverName" name="co_driver_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_phoneNumber">Phone Number</label>
                            <input type="text" class="form-control" id="edit_phoneNumber" name="phone_number" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_emailAddress">Email Address</label>
                            <input type="email" class="form-control" id="edit_emailAddress" name="email_address" required>
                        </div>
                        <button type="submit" class="btn btn-orange">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Co-Driver Modal -->
    <div class="modal fade" id="deleteCoDriverModal" tabindex="-1" aria-labelledby="deleteCoDriverModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCoDriverModalLabel">Delete Co-Driver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this co-driver?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCoDriverButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    
    </script>
</body>
</html>
