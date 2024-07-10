<?php
$appointment_id = $params[0];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form
    $test_id = $_POST['test_id'];
    $results = $_POST['results'];
    $remark = $_POST['remark'];
    // Additional processing if needed
    $results_string = implode(",", $results); // Convert results array to a string
    //Insert into database
    $stmt = $pdo->prepare("INSERT INTO reports (appointment_id, test_id, results,remarks, created_at, updated_at) 
                           VALUES (:appointment_id, :test_id, :results,:remarks, NOW(), NOW())");
    $stmt->bindParam(':appointment_id', $appointment_id); // Ensure you have $appointment_id defined
    $stmt->bindParam(':test_id', $test_id);
    $stmt->bindParam(':results', $results_string);
    $stmt->bindParam(':remarks', $remark);
    
    if ($stmt->execute()) {
        echo "Data inserted successfully.";
    } else {
        echo "Error inserting data.";
    }
    print_r($results);

    // Redirect to the same page after form submission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit; // Stop further execution after handling POST request
}


$stmt = $pdo->query("SELECT 
            a.appointment_id, 
            t.test_name,
            t.test_id
        FROM 
            appointment a
        LEFT JOIN 
            test t ON FIND_IN_SET(t.test_id, a.test_ids)
        WHERE 
            a.appointment_id = $appointment_id");

$result = $stmt->fetchAll();

$appointments = [];

// print_r($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
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
    <div class="container mt-3">
        <select class="form-control" id="tests" name="test">
            <?php foreach ($result as $app): ?>
                <option value="<?php echo $app['test_id']; ?>"><?php echo $app['test_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <form action="" id="subtestForm" method="post">
        <h2></h2>
        <table id="appointmentsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Subtest Name</th>
                    <th>Normal Ranges</th>
                    <th>Results</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>


        </form>
    </div>

    <!-- Include necessary scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            function fetchSubtests(testId) {
                if (testId) {
                    $.ajax({
                        url: '/myLab/get-subtests',
                        type: 'GET',
                        data: { test_id: testId },
                        success: function(response) {
                            var subtests = JSON.parse(response);
                            var tbody = $('#appointmentsTable tbody');
                            tbody.empty();
                            $('#subtestbtn').remove()
                            subtests.forEach(function(subtest) {
                                var row = '<tr>' +
                                    '<td>' + subtest.subtest_name + '</td>' +
                                    '<td>' + subtest.normal_ranges + '</td>' +
                                    `<td><input type="text" class="form-control" id="results" name="results[]" placeholder="" required></td>` +
                                    '</tr>';
                                tbody.append(row);
                            });
                            $('#subtestForm').append(`<div id="subtestbtn" class="row">
                            <div class="mb-3 col-8">
                                <label for="remark" class="form-label">Remark</label>
                                <textarea class="form-control" aria-label="With textarea" id="remark" name="remark"></textarea>
                                </div>
                            <div class="col-3 text-right">
                            <input type="hidden" class="form-control" id="test_id" name="test_id" value="${testId}" placeholder="">
                            <button id="subtestFormbtn" class="btn btn-primary mt-5">submit</button>
                            </div>
                            </div>`)
                        }
                    });
                }
            }

            $('#tests').change(function() {
                var testId = $(this).val();
                fetchSubtests(testId);
            });

            // Trigger change event on page load if a test is selected
            var initialTestId = $('#tests').val();
            if (initialTestId) {
                fetchSubtests(initialTestId);
            }
        });
    </script>
</body>
</html>
