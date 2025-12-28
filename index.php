<?php
// Include the database connection
include 'db_connect.php';

// Fetch all events ordered by date_event descending (newest first)
try {
    $stmt = $pdo->prepare("SELECT * FROM events ORDER BY date_event DESC");
    $stmt->execute();
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching events: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniEvent - Home</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">UniEvent</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="add_event.php">Admin Panel</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="display-4">Welcome to UniEvent</h1>
                    <p class="lead">Discover and register for exciting university events. Stay connected with your campus community!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Section -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Upcoming Events</h2>
        <?php if (empty($events)): ?>
            <div class="text-center">
                <p class="text-muted">No upcoming events at the moment. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($events as $event): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($event['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($event['image_path']); ?>" class="card-img-top" alt="Event Image" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                <p class="card-text">
                                    <strong>Date:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($event['date_event'])); ?><br>
                                    <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
                                </p>
                                <button class="btn btn-primary mt-auto register-btn" data-event-id="<?php echo $event['id']; ?>" data-bs-toggle="modal" data-bs-target="#registerModal">Register Now</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register for Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <input type="hidden" id="event_id" name="event_id">
                        <div class="mb-3">
                            <label for="student_name" class="form-label">Student Name</label>
                            <input type="text" class="form-control" id="student_name" name="student_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="student_email" class="form-label">Student Email</label>
                            <input type="email" class="form-control" id="student_email" name="student_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="Student">Student</option>
                                <option value="Professor">Professor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="university_id" class="form-label">University ID / Matricule</label>
                            <input type="text" class="form-control" id="university_id" name="university_id" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Registration</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery Script for AJAX -->
    <script>
        $(document).ready(function() {
            // When Register Now button is clicked, set the event ID in the modal
            $('.register-btn').on('click', function() {
                var eventId = $(this).data('event-id');
                $('#event_id').val(eventId);
            });

            // Handle form submission via AJAX
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'process_register.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#registerModal').modal('hide');
                            alert('Registration Successful!');
                            // Optionally reset the form
                            $('#registerForm')[0].reset();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>