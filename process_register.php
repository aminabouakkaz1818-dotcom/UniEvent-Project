<think>
First, the user wants the full code for "process_register.php" rewritten with new validation logic.

</think>```php
<?php
// Include the database connection
include 'db_connect.php';

// Set header for JSON response
header('Content-Type: application/json');

// Get POST data
$event_id = $_POST['event_id'] ?? '';
$student_name = trim($_POST['student_name'] ?? '');
$student_email = trim($_POST['student_email'] ?? '');
$role = $_POST['role'] ?? '';
$university_id = trim($_POST['university_id'] ?? '');

// Validate input: All fields except university_id for professors are required
if (empty($event_id) || empty($student_name) || empty($student_email) || empty($role)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// Validate email format
if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
    exit;
}

// Role-based validation
if ($role === 'Student') {
    // For students: Any valid email, but university_id is mandatory
    if (empty($university_id)) {
        echo json_encode(['status' => 'error', 'message' => 'University ID / Matricule is required for students.']);
        exit;
    }
} elseif ($role === 'Professor') {
    // For professors: Email must end with @univ-skikda.dz, university_id is optional
    if (!preg_match('/@univ-skikda\.dz$/', $student_email)) {
        echo json_encode(['status' => 'error', 'message' => 'Access Denied! Professors must use their official university email (@univ-skikda.dz).']);
        exit;
    }
    // university_id is optional, so no check needed
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid role selected.']);
    exit;
}

try {
    // Insert into registrations table
    $stmt = $pdo->prepare("INSERT INTO registrations (event_id, student_name, student_email, role, university_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$event_id, $student_name, $student_email, $role, $university_id]);
    
    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Registered!']);
} catch (PDOException $e) {
    // Return error response
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
```