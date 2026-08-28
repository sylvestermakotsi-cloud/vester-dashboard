<?php
require __DIR__ . '/inquiry-mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$required = ['fname', 'lname', 'email', 'tel', 'nationality', 'address', 'county', 'company', 'desc'];
foreach ($required as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        header('Location: index.html?status=error');
        exit;
    }
}

$data = [
    'fname' => trim($_POST['fname']),
    'lname' => trim($_POST['lname']),
    'email' => trim($_POST['email']),
    'tel' => trim($_POST['tel']),
    'nationality' => trim($_POST['nationality']),
    'address' => trim($_POST['address']),
    'county' => trim($_POST['county']),
    'company' => trim($_POST['company']),
    'desc' => trim($_POST['desc']),
    'server' => trim($_POST['server'] ?? 'Not provided'),
];

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    header('Location: index.html?status=error');
    exit;
}

if (sendInquiryEmail($data)) {
    header('Location: index.html?status=success');
} else {
    header('Location: index.html?status=error');
}

exit;
?>
