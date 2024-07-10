<?php
    require 'db.php';


$test_id = $_GET['appointment_id'];

$stmt = $pdo->prepare("SELECT 
    r.report_id,
    t.test_name
FROM 
    reports r
JOIN 
    test t ON r.test_id = t.test_id
WHERE 
    r.appointment_id = ?
");
$stmt->execute([$test_id]);

$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($reports);
?>
