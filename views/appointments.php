
<?php
$stmt = $pdo->query("SELECT
    a.appointment_id AS 'Appointment ID',
    CONCAT(p.first_name, ' ', p.last_name) AS 'Patient Name',
    GROUP_CONCAT(t.test_name ORDER BY t.test_id SEPARATOR ', ') AS 'Tests',
    a.status AS 'Status',
    a.charges AS 'Charges',
    a.prescribed_by AS 'Prescribed By',
    DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s') AS 'Date'
FROM
    appointment a
JOIN
    patient p ON a.patient_id = p.patient_id
LEFT JOIN
    test t ON FIND_IN_SET(t.test_id, a.test_ids)
GROUP BY
    a.appointment_id, p.first_name, p.last_name, a.status, a.charges, a.prescribed_by, a.created_at
ORDER BY
    a.created_at DESC;
 -- Adjust ordering as per your requirement
");
$appointments = $stmt->fetchAll();

//  print_r($appointments);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .registration-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
       

        <!-- DataTable to display appointments -->
        <div class="card mt-4">
            <div class="card-header row justify-content-between">
                <div class="col-8">
                    <h6>Appointments</h6>
                </div>
                <div class="col-3 text-right">
                <a href="registration" class="btn btn-sm btn-primary float-end">Register</a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table id="appointmentsTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient Name</th>
                            <th>Tests</th>
                            <th>Status</th>
                            <th>Charges</th>
                            <th>Prescribed By</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($appointments as $app): ?>
                        <tr>
                        <td><?php echo $app['Appointment ID'] ?></td>
                        <td><?php echo $app['Patient Name'] ?></td>
                        <td><?php echo $app['Tests'] ?></td>
                        <td><?php echo $app['Status'] ?></td>
                        <td><?php echo $app['Charges'] ?></td>
                        <td><?php echo $app['Prescribed By'] ?></td>
                        <td><?php echo $app['Date'] ?></td>
                        <td>
                            <a href="/myLab/generate-reports/<?php echo $app['Appointment ID']; ?>" class="btn btn-sm btn-primary mb-1">Generate Report</a>
                            <button type="button" class="btn btn-dark btn-sm" onclick="fetchReports(<?php echo $app['Appointment ID']; ?>)">view reports</button>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                        <!-- Appointment data will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reportsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="reportsModalBody">
        ...
      </div>
      
    </div>
  </div>
</div>
    <!-- Include necessary scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // $('.select2').select2();

            // Initialize DataTable
            $('#appointmentsTable').DataTable({  
            });
            
        });

        function fetchReports(appId) {
                if (appId) {
                    $.ajax({
                        url: '/myLab/get-reports',
                        type: 'GET',
                        data: { appointment_id: appId },
                        success: function(response) {
                            var reports = JSON.parse(response);
                            var  modalBody= $('#reportsModalBody');
                            modalBody.empty();
                            console.log(reports)
                            modalBody.append('<ul class="list-group">')

                            reports.forEach(function(report){
                                var row = `<li class="list-group-item">
                                <div class="row justify-content-between ">
                                <div class="col-4">${report.test_name}</div>
                                <div class="col-4"><a href="report/${report.report_id}" class="btn btn-sm btn-success float-end">view report</a></div>
                                </div>
                                </li>`
                                modalBody.append(row)
                            })
                            modalBody.append('</ul>')
                            $('#reportsModal').modal('show');

                            // $('#subtestbtn').remove()
                            // subtests.forEach(function(subtest) {
                            //     var row = '<tr>' +
                            //         '<td>' + subtest.subtest_name + '</td>' +
                            //         '<td>' + subtest.normal_ranges + '</td>' +
                            //         `<td><input type="text" class="form-control" id="results" name="results[]" placeholder="" required></td>` +
                            //         '</tr>';
                            //         modalBody.append(row);
                            // });
                        }
                    });
                }
            }
        $(document).ready(function() {
            function fetchReports(appId) {
                if (testId) {
                    $.ajax({
                        url: '/myLab/get-reports',
                        type: 'GET',
                        data: { appointment_id: appId },
                        success: function(response) {
                            var reports = JSON.parse(response);
                            var  modalBody= $('#reportsModalBody');
                            modalBody.empty();
                            console.log(reports)
                            // $('#subtestbtn').remove()
                            // subtests.forEach(function(subtest) {
                            //     var row = '<tr>' +
                            //         '<td>' + subtest.subtest_name + '</td>' +
                            //         '<td>' + subtest.normal_ranges + '</td>' +
                            //         `<td><input type="text" class="form-control" id="results" name="results[]" placeholder="" required></td>` +
                            //         '</tr>';
                            //         modalBody.append(row);
                            // });
                        }
                    });
                }
            }

            // $('#tests').change(function() {
            //     var testId = $(this).val();
            //     fetchSubtests(testId);
            // });

            // // Trigger change event on page load if a test is selected
            // var initialTestId = $('#tests').val();
            // if (initialTestId) {
            //     fetchSubtests(initialTestId);
            // }
        });

    </script>
</body>
</html>
