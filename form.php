<?php

function loadEnvFile(string $path): void
{
    if (!is_file($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || substr($trimmed, 0, 1) === '#') {
            continue;
        }

        if (strpos($trimmed, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $trimmed, 2);
        $key = trim($key);
        $value = trim($value);

        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') || (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

loadEnvFile(__DIR__ . '/.env');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

function clean(string $value): string
{
    return trim(strip_tags($value));
}

$firstName = clean($_POST['fname'] ?? '');
$lastName = clean($_POST['lname'] ?? '');
$email = filter_var(clean($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone = clean($_POST['tel'] ?? '');
$server = clean($_POST['server'] ?? '');
$nationality = clean($_POST['nationality'] ?? '');
$address = clean($_POST['address'] ?? '');
$county = clean($_POST['county'] ?? '');
$company = clean($_POST['company'] ?? '');
$description = clean($_POST['desc'] ?? '');

if ($firstName === '' || $lastName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $description === '') {
    header('Location: index.html?status=error');
    exit;
}

$to = getenv('MAIL_TO') ?: 'sylvestermakotsi@gmail.com';
$fromEmail = getenv('SMTP_FROM') ?: 'noreply@vesterhelp.com';
$fromName = getenv('SMTP_FROM_NAME') ?: 'Vester Help';
$subject = 'New client inquiry from ' . $firstName . ' ' . $lastName;

$body = "You have received a new inquiry from the Vester Help website.\n\n";
$body .= "Name: {$firstName} {$lastName}\n";
$body .= "Email: {$email}\n";
$body .= "Phone: {$phone}\n";
$body .= "Phone Network: {$server}\n";
$body .= "Nationality: {$nationality}\n";
$body .= "Address: {$address}\n";
$body .= "County: {$county}\n";
$body .= "Company: {$company}\n\n";
$body .= "Project Description:\n{$description}\n";

$headers = "From: {$fromName} <{$fromEmail}>\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$success = false;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USERNAME') ?: $fromEmail;
        $mail->Password = getenv('SMTP_PASSWORD') ?: '';
        $mail->SMTPSecure = getenv('SMTP_SECURE') ?: 'tls';
        $mail->Port = (int) (getenv('SMTP_PORT') ?: 587);
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to, 'Vester Help');
        $mail->addReplyTo($email, $firstName . ' ' . $lastName);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;

        $success = $mail->send();
    } catch (Exception $e) {
        $success = false;
    }
}

if (!$success && function_exists('mail')) {
    $success = mail($to, $subject, $body, $headers);
}

if ($success) {
    header('Location: index.html?status=success');
    exit;
}

header('Location: index.html?status=error');
exit;
