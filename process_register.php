<?php

include 'db_connect.php';

header('Content-Type: application/json');

$event_id = $_POST['event_id'] ?? '';
$student_name = trim($_POST['student_name'] ?? '');
$student_email = trim($_POST['student_email'] ?? '');
$role = $_POST['role'] ?? '';
$university_id = trim($_POST['university_id'] ?? '');

if (empty($event_id) || empty($student_name) || empty($student_email) || empty($role)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
    exit;
}

if ($role === 'Student') {
    if (empty($university_id)) {
        echo json_encode(['status' => 'error', 'message' => 'University ID / Matricule is required for students.']);
        exit;
    }
} elseif ($role === 'Professor') {
    if (!preg_match('/@univ-skikda\.dz$/', $student_email)) {
        echo json_encode(['status' => 'error', 'message' => 'Access Denied! Professors must use their official university email (@univ-skikda.dz).']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid role selected.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE event_id = ? AND student_email = ?");
    $stmt->execute([$event_id, $student_email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'You are already registered for this event.']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error during duplicate check: ' . $e->getMessage()]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO registrations (event_id, student_name, student_email, role, university_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$event_id, $student_name, $student_email, $role, $university_id]);
    
    echo json_encode(['status' => 'success', 'message' => 'Registered!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>