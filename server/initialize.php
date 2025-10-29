<?php

session_start();

include __DIR__ . "/services.php";

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