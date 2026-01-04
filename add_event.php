<think>

</think>
<?php

include 'db_connect.php';


$alertMessage = '';
$alertType = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $admin_code = $_POST['admin_code'] ?? '';
    if ($admin_code !== 'admin123') {
        $alertMessage = 'Invalid Admin Code! Event not added.';
        $alertType = 'danger';
    } else {
    
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $date_event = $_POST['date_event'];
        $location = trim($_POST['location']);
     
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
           
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = basename($_FILES['image']['name']);
            $targetFile = $uploadDir . uniqid() . '_' . $fileName; 
         
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $image_path = $targetFile;
                } else {
                    $alertMessage = 'Error uploading the image.';
                    $alertType = 'danger';
                }
            } else {
                $alertMessage = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
                $alertType = 'danger';
            }
        } else {
            $alertMessage = 'Image is required.';
            $alertType = 'danger';
        }
        

        if (empty($alertMessage)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO events (title, description, date_event, location, image_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $date_event, $location, $image_path]);
                $alertMessage = 'Event added successfully!';
                $alertType = 'success';
            } catch (PDOException $e) {
                $alertMessage = 'Error adding event: ' . $e->getMessage();
                $alertType = 'danger';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - UniEvent Admin</title>
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="text-center mb-4">Add New Event</h1>
                
                
                <?php if (!empty($alertMessage)): ?>
                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $alertMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
               
                <form method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow">
                    <div class="mb-3">
                        <label for="admin_code" class="form-label">Admin Code</label>
                        <input type="password" class="form-control" id="admin_code" name="admin_code" required>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="date_event" class="form-label">Date & Time</label>
                        <input type="datetime-local" class="form-control" id="date_event" name="date_event" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Event</button>
                </form>
            </div>
        </div>
    </div>
    
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```