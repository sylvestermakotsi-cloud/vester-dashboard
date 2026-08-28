<?php
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $nationality = $_POST['nationality'];
    $address = $_POST['address'];
    $county = $_POST['county'];
    $company = $_POST['company'];
    $desc = $_POST['desc'];


    $email_from = 'info@vesterhelp.com';
    $email-body = "First Name: $fname.\n".
                  "Last Name: $lname.\n".
                  "Email Address: $email.\n".
                  "Phone Number: $tel.\n".
                  "Nationality: $nationality.\n".
                  "Address: $address.\n".
                  "County: $county.\n".
                  "Company: $company.\n".
                  "Project Description: $desc.\n";

    $to = "sylvestermakotsi@gmail.com";
    $headers = "From: $email_from \r\n";
    $headers .= "Reply-To: $email \r\n";


    mail($to,$email_subject,$email_body,$headers);
    header("Location: index.html");




?>
