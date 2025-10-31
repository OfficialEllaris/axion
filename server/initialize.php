<?php

session_start();

include __DIR__ . "/services.php";

if (!isset($_SESSION['users_table'])) {
    $_SESSION['users_table'] = [];
}

if (isset($_POST['onboard_user'])) {
    onboardUser($_POST);
    header('Location: ./onboarding.php');
    exit();
}

if (isset($_POST['login_user'])) {
    loginUser($_POST);
    header('Location: ./login.php');
    exit();
}

if (isset($_POST['logout_user'])) {
    // Only clear the logged-in user data
    unset($_SESSION['logged_in_user']);

    $_SESSION['flash_message'] = "You’ve been logged out successfully.";

    // Redirect back to login page
    header('Location: ./login.php');

    exit();
}

if (isset($_POST['reset_password'])) {
    resetPassword($_POST);
    header('Location: ./account-recovery.php');
    exit();
}

if (isset($_POST['update_settings'])) {
    updateSettings($_POST);
    header('Location: ./settings.php');
    exit();
}