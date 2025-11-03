<?php

session_start();

include __DIR__ . "/services.php";

if (!isset($_SESSION['users_table'])) {
    $_SESSION['users_table'] = [];
}

if (!isset($_SESSION['projects_table'])) {
    $_SESSION['projects_table'] = [];
}

if (isset($_POST['onboard_user'])) {
    onboardUser($_POST);
    header('Location: ./onboarding');
    exit();
}

if (isset($_POST['login_user'])) {
    loginUser($_POST);
    header('Location: ./login');
    exit();
}

if (isset($_POST['logout_user'])) {
    // Only clear the logged-in user data
    unset($_SESSION['logged_in_user']);

    $_SESSION['flash_message'] = "You’ve been logged out successfully.";

    // Redirect back to login page
    header('Location: ./login');

    exit();
}

if (isset($_POST['reset_password'])) {
    resetPassword($_POST);
    header('Location: ./account-recovery');
    exit();
}

if (isset($_POST['update_profile'])) {
    updateProfile($_POST);
    header('Location: ./settings');
    exit();
}

if (isset($_POST['update_password'])) {
    updatePassword($_POST);
    header('Location: ./settings');
    exit();
}

if (isset($_POST['create_project'])) {
    createProject($_POST);
    header('Location: ./dashboard');
    exit();
}

if (isset($_POST['delete_project'])) {
    deleteProject($_POST);
    header('Location: ./dashboard');
    exit();
}

if (isset($_POST['modify_project'])) {
    modifyProject($_POST);
    header('Location: ./manage-project?project_id=' . $_GET['project_id']);
    exit();
}
