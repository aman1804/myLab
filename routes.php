<?php
$routes = [
    '' => 'views/home.php',  // Home route
    'about' => 'views/about.php',
    'contact' => 'views/contact.php',
    'registration' => 'views/patient-register.php',
    'appointments' => 'views/appointments.php',
    'generate-reports/{id}' => 'views/generate-reports.php',
    'get-subtests' => 'controllers\get_subtests.php',
    'get-reports' => 'controllers\get-reports.php',
    'report/{id}' => 'views\report.php',

];
?>
