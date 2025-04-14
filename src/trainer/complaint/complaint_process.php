<?php

require_once "../../alerts/functions.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect_with_error_alert("Method not allowed", "./");
}

require_once "../../auth-guards.php";
auth_required_guard("trainer", "../login");

require_once "../../db/models/Complaint.php";

$type = htmlspecialchars($_POST['type']);
$description = trim(htmlspecialchars($_POST['description']));

if (empty($description)) {
    redirect_with_error_alert("Description cannot be empty", "./");
}

$userId = $_SESSION['auth']['id'];

$complaint = new Complaint();

$complaint->fill([
    'type' => $type,
    'description' => $description,
    'user_id' => $userId,
    'is_created_by_trainer' => true
]);

try {
    $complaint->create();
} catch (PDOException $e) {
    redirect_with_error_alert("An error occurred: " . $e->getMessage(), "./");
}

require_once "../../notifications/functions.php";

try {
    new_notification_to_trainers([$_SESSION['auth']['id']], "New complaint submitted", "Your complaint has been submitted successfully. We will review it and get back to you soon.", null);
} catch (\Throwable $th) {
    redirect_with_info_alert("Complaint submitted, but failed to send notification: " . $th->getMessage(), "./");
}

redirect_with_success_alert("Complaint submitted successfully.", "./");
