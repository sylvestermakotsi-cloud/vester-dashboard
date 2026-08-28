<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$required_fields = array('fname', 'lname', 'email', 'tel', 'nationality', 'address', 'county', 'company', 'desc');
$fields = array();

foreach ($required_fields as $field) {
    $value = isset($_POST[$field]) ? trim((string) $_POST[$field]) : '';

    if ($value === '') {
        http_response_code(400);
        exit('Please complete all required fields.');
    }

    $fields[$field] = $value;
}

if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Please provide a valid email address.');
}

$to = 'sylvestermakotsi@gmail.com';
$subject = 'New website contact form submission';
$message = "First Name: {$fields['fname']}\n"
    . "Last Name: {$fields['lname']}\n"
    . "Email Address: {$fields['email']}\n"
    . "Phone Number: {$fields['tel']}\n"
    . "Nationality: {$fields['nationality']}\n"
    . "Address: {$fields['address']}\n"
    . "County: {$fields['county']}\n"
    . "Company: {$fields['company']}\n"
    . "Project Description: {$fields['desc']}\n";

$headers = "From: info@vester-two.com\r\n"
    . "Reply-To: {$fields['email']}\r\n"
    . "Content-Type: text/plain; charset=UTF-8\r\n";

if (!mail($to, $subject, $message, $headers)) {
    http_response_code(500);
    exit('The message could not be sent. Please try again later.');
}

header('Location: index.html?sent=1');
exit;
?>
