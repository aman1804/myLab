<?php
    require 'db.php';
// Assuming you have established a PDO connection earlier

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from POST
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    // Retrieve other form fields similarly

    // Insert into patients table
    $stmt = $pdo->prepare("INSERT INTO patient (first_name, last_name, age, gender, mobile, email, address, created_at, updated_at)
                           VALUES (:firstName, :lastName, :age, :gender, :mobile, :email, :address, NOW(), NOW())");
    $stmt->execute([
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':age' => $age,
        ':gender' => $gender,
        ':mobile' => $mobile,
        ':email' => $email,
        ':address' => $address,
        // Bind other form field values here
    ]);

    // Retrieve last inserted patient_id
    $patientId = $pdo->lastInsertId();

    // Insert into appointments table
    $testIds = json_decode($_POST['test-prescribed']); // Assuming selectedTests is a JSON array of test IDs
    
        $stmt = $pdo->prepare("INSERT INTO appointment (patient_id, test_ids, status, charges, prescribed_by, created_at, updated_at)
                               VALUES (:patientId, :testIds, :status, :charges, :prescribedBy, NOW(), NOW())");
        $stmt->execute([
            ':patientId' => $patientId,
            ':testIds' => $testIds,
            ':status' => 'Scheduled', // Example status
            ':charges' => $_POST['charges'], // You might calculate charges differently
            ':prescribedBy' => $_POST['prescribed-by'], // Assuming you have this field in your form
            // Add other values as needed
        ]);
    

    // Optionally, redirect after successful insertion
    header("Location: /myLab/appointments");
    exit();
}
?>
