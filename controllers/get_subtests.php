<?php
    require 'db.php';


$test_id = $_GET['test_id'];

$stmt = $pdo->prepare("SELECT 
            st.subtest_name, 
            st.normal_ranges 
        FROM 
            subtest st
        LEFT JOIN 
            test t ON st.test_id = t.test_id
        WHERE 
            t.test_id = ?");
$stmt->execute([$test_id]);

$subtests = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($subtests);
?>
