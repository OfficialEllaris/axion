<?php

function onboardUser($data)
{
    if (
        !empty($data['fullname']) &&
        !empty($data['email_address']) &&
        !empty($data['password'])
    ) {
        // Set cookies for simplicity (not secure for real apps)
        setcookie('fullname', $data['fullname']);
        setcookie('email_address', $data['email_address']);
        setcookie('password', $data['password']);

        $_SESSION['flash_message'] = "Account created successfully!";
    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}

function loginUser($data)
{
    if (!empty($data['email_address']) && !empty($data['password'])) {

        // Check if cookies exist before comparing
        if (
            isset($_COOKIE['email_address'], $_COOKIE['password'], $_COOKIE['fullname']) &&
            $data['email_address'] === $_COOKIE['email_address'] &&
            $data['password'] === $_COOKIE['password']
        ) {
            $fullname = htmlspecialchars($_COOKIE['fullname']);
            $_SESSION['flash_message'] = "Welcome back, $fullname!";
            header('Location: ./dashboard.php');
            exit(); // important: stop execution after redirect
        } else {
            $_SESSION['flash_message'] = "Invalid account credentials!";
        }

    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}
