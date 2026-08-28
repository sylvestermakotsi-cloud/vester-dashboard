<?php

if (file_exists(__DIR__ . '/.env')) {
    $envLines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($value !== '') {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    error_log('PHPMailer is not installed yet. Run: cd "' . __DIR__ . '" && composer install');

    function buildInquiryBody(array $data): string
    {
        $lines = [
            'First Name: ' . $data['fname'],
            'Last Name: ' . $data['lname'],
            'Email Address: ' . $data['email'],
            'Phone Number: ' . $data['tel'],
            'Phone Network: ' . ($data['server'] ?? 'Not provided'),
            'Nationality: ' . $data['nationality'],
            'Address: ' . $data['address'],
            'County: ' . $data['county'],
            'Company: ' . $data['company'],
            'Project Description: ' . $data['desc'],
        ];

        return implode("\r\n", $lines);
    }

    function sendInquiryEmail(array $data): bool
    {
        return false;
    }

    return;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

function buildInquiryBody(array $data): string
{
    $lines = [
        'First Name: ' . $data['fname'],
        'Last Name: ' . $data['lname'],
        'Email Address: ' . $data['email'],
        'Phone Number: ' . $data['tel'],
        'Phone Network: ' . ($data['server'] ?? 'Not provided'),
        'Nationality: ' . $data['nationality'],
        'Address: ' . $data['address'],
        'County: ' . $data['county'],
        'Company: ' . $data['company'],
        'Project Description: ' . $data['desc'],
    ];

    return implode("\r\n", $lines);
}

function sendInquiryEmail(array $data): bool
{
    $host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $username = getenv('SMTP_USERNAME') ?: 'your-email@gmail.com';
    $password = getenv('SMTP_PASSWORD') ?: '';
    $fromEmail = getenv('SMTP_FROM') ?: $username;
    $fromName = getenv('SMTP_FROM_NAME') ?: 'Vester Help';
    $toEmail = getenv('MAIL_TO') ?: 'sylvestermakotsi@gmail.com';
    $port = (int) (getenv('SMTP_PORT') ?: 587);
    $secure = getenv('SMTP_SECURE') ?: 'tls';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->Port = $port;
        $mail->CharSet = 'UTF-8';

        if (strtolower($secure) === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif (strtolower($secure) === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPAutoTLS = false;
        }

        $mail->setFrom($fromEmail, $fromName);
        $mail->addReplyTo($data['email'], $data['fname'] . ' ' . $data['lname']);
        $mail->addAddress($toEmail, 'Vester Help');
        $mail->Subject = 'New project inquiry from ' . $data['fname'] . ' ' . $data['lname'];
        $mail->Body = buildInquiryBody($data);
        $mail->AltBody = strip_tags(buildInquiryBody($data));

        return $mail->send();
    } catch (Exception $e) {
        error_log('PHPMailer error: ' . $mail->ErrorInfo . ' - ' . $e->getMessage());
        return false;
    }
}
