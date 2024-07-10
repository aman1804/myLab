<?php
// Assuming $pdo is your PDO connection and $params is the array containing your report ID.
$report_id = $params[0];

// Step 1: Fetch the report details
$stmt = $pdo->prepare('SELECT * FROM reports WHERE report_id = ?');
$stmt->execute([$report_id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if ($report) {
    $test_id = $report['test_id'];
    $appointment_id = $report['appointment_id'];

    // Step 2: Fetch the subtests
    $stmtSubtests = $pdo->prepare('SELECT * FROM subtest WHERE test_id = ?');
    $stmtSubtests->execute([$test_id]);
    $subtests = $stmtSubtests->fetchAll(PDO::FETCH_ASSOC);

    // Step 3: Fetch the patient details using appointment_id
    $stmtPatient = $pdo->prepare('SELECT p.*, a.prescribed_by FROM patient p JOIN appointment a ON p.patient_id = a.patient_id WHERE a.appointment_id = ?');
    $stmtPatient->execute([$appointment_id]);
    $patient = $stmtPatient->fetch(PDO::FETCH_ASSOC);


    $testNamestmt = $pdo->prepare('SELECT test_name, test_desc FROM test WHERE test_id = ?');
    $testNamestmt->execute([$test_id]);
    $testName = $testNamestmt->fetch(PDO::FETCH_ASSOC);
    
   
    
    // Step 4: Print the results

   
    // echo "Report Details:\n";
    // print_r($report);
    // echo "\nSubtests:\n";
    // print_r($subtests);
    // echo "\nPatient Details:\n";
    // print_r($patient);
    $result_values = explode(',', $report['results']);
    
    // $count = count($subtests);
    // echo($testName['test_name']);
} else {
    echo "No report found with ID: $report_id";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CBC Report</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .report-header, .report-footer {
      border-bottom: 1px solid #000;
      padding-bottom: 10px;
      margin-bottom: 10px;
    }
    .report-section {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Report Header -->
     <div style="height: 10vh;"></div>
    <div class="report-header row " style="display: flex;  margin: 2px; padding: 2px; gap: 2px; ">
      <div class="col-md-8" style="flex: 1;">
      <h3><?php echo ucwords(strtolower($patient['first_name'] . ' ' . $patient['last_name'])); ?></h3>

        <p><?php echo $patient['age'].'Y/'.$patient['gender']; ?></p>
        <p>Patient No: <?php echo 'P0000'.$patient['patient_id']; ?></p>
        <p>Referred By: <?php echo $patient['prescribed_by'];?> </p>
      </div>
      <div class="col-md-4 text-right" style="flex: 0.5; text-align: start; float:right;">
        <p>Report No: <?php echo 'REP000'.$report_id; ?></p>
        <p>Registered Date: <?php echo $patient['created_at'] ?></p>
        <p>Reported Date: <?php echo $report['created_at'] ?></p>
        <p>Report Printed on: <?php echo date('Y/m/d'); ?></p>
      </div>
    </div>
    
    <!-- Report Body -->
    <div class="report-body">
      
      <div class="report-section">
        <h6><?php echo $testName['test_desc'];?></h6>
        <table class="table table-borderless">
          <thead class="border-bottom">
            <tr>
              <th class="text-center" >Test Description</th>
              <th class="text-center" >Result</th>
              <th class="text-center" >Reference Range</th>
            </tr>
          </thead>
          
          <tbody>
          <?php foreach ($subtests as $key => $app) : ?>
            <tr>
                <td class="text-center" ><?php echo $app['subtest_name']; ?></td>
                <td class="text-center" ><?php echo isset($result_values[$key]) ? $result_values[$key] : ''; ?></td>
                <td class="text-center" ><?php echo $app['normal_ranges']; ?></td>
            </tr>
        <?php endforeach; ?>

           
          </tbody>
        </table>
      </div>
    </div>

    <!-- Report Footer -->
    <div class="report-footer row" style="display: flex;  margin: 2px; padding: 2px; gap: 2px; ">
        <div class="col-12 mb-4">
        <td><?php echo $report['remarks']?'<b>Remark : </b>'.$report['remarks']:''; ?></td>
        </div>
      <div class="col-md-4" style="flex: 1;">
        <p>Checked By : ______________________</p>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
