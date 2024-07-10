<?php
$stmt = $pdo->query('SELECT * FROM test');
$tests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
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
        <div class="registration-container">
            <h2 class="text-center">Patient Registration</h2>
            <form action="controllers\register.php" method="post">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" placeholder="Enter first name" name="firstName">
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" placeholder="Enter last name" name="lastName">
                </div>
                <div class="form-group">
                    <label for="dob">Age</label>
                    <input type="number" class="form-control" id="age" name="age">
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select class="form-control" id="gender" name="gender">
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" placeholder="Enter phone number" name="mobile">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" name="address" id="address" rows="3" placeholder="Enter address"></textarea>
                </div>
                <div class="form-group">
                    <label for="prescribed-by">Prescribed By</label>
                    <input type="text" class="form-control" id="prescribed-by" name="prescribed-by" placeholder="">
                </div>
                <div class="form-group">
                    <label for="test-prescribed">Test Prescribed</label>
                    <select class="form-control select2" id="test-prescribed" name="test-prescribed" multiple="multiple">
                        <?php foreach ($tests as $test): ?>
                        <option value="<?php echo $test['test_id']; ?>"><?php echo $test['test_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="charges">Charges</label>
                    <input type="text" class="form-control" id="charges" name="charges" placeholder="Charges will appear here" readonly>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#test-prescribed').on('change', function() {
                var selectedTests = $(this).val();
                var charges = 0;
                if (selectedTests) {
                    selectedTests.forEach(function(testId) {
                        var test = <?php echo json_encode($tests); ?>.find(function(t) {
                            return t.test_id == testId;
                        });
                        // console.log(test)
                        if (test) {
                            // console.log(charges)
                            charges += parseFloat(test.cost);
                        }
                    });
                }
                $('#charges').val(charges.toFixed(2));
            });
        });
    </script>
</body>
</html>
