<?php

function onboardUser($data)
{
    if (
        !empty($data['fullname']) &&
        !empty($data['email_address']) &&
        !empty($data['password'])
    ) {
        // Hash the password with bcrypt
        $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);

        $new_user = [
            'fullname' => $data['fullname'],
            'email_address' => $data['email_address'],
            'password' => $hashed_password
        ];

        // Append new user to session
        $_SESSION['users_table'][] = $new_user;

        $_SESSION['flash_message'] = "Account created successfully!";
    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}


function loginUser($data)
{
    if (!empty($data['email_address']) && !empty($data['password'])) {

        if (isset($_SESSION['users_table']) && !empty($_SESSION['users_table'])) {
            $found_user = null;

            // First: find user by email
            foreach ($_SESSION['users_table'] as $user) {
                if ($user['email_address'] === $data['email_address']) {
                    $found_user = $user;
                    break;
                }
            }

            if ($found_user) {
                // Then: verify password
                if (password_verify($data['password'], $found_user['password'])) {
                    $fullname = htmlspecialchars($found_user['fullname']);
                    $_SESSION['logged_in_user'] = $found_user;
                    $_SESSION['flash_message'] = "Welcome back, $fullname!";
                    header('Location: ./dashboard.php');
                    exit();
                } else {
                    $_SESSION['flash_message'] = "Invalid account credentials.";
                }
            } else {
                $_SESSION['flash_message'] = "The provided email address does not exist in our system.";
            }

        } else {
            $_SESSION['flash_message'] = "No users found. Please sign up first.";
        }

    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}


function resetPassword($data)
{
    if (
        !empty($data['email_address']) &&
        !empty($data['password']) &&
        !empty($data['password_confirmation'])
    ) {
        if ($data['password'] !== $data['password_confirmation']) {
            $_SESSION['flash_message'] = "Passwords do not match.";
            return;
        }

        if (isset($_SESSION['users_table']) && !empty($_SESSION['users_table'])) {
            $found = false;
            foreach ($_SESSION['users_table'] as &$user) { // use reference '&' to persist changes
                if ($user['email_address'] === $data['email_address']) {
                    $user['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $_SESSION['flash_message'] = "Account recovery successful!";
                header('Location: ./login.php');
                exit();
            } else {
                $_SESSION['flash_message'] = "The provided email address does not exist in our system.";
            }
        } else {
            $_SESSION['flash_message'] = "No users found. Please sign up first.";
        }
    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}

function updateSettings($data)
{
    if (
        !empty($data['fullname']) &&
        !empty($data['email_address'])
    ) {
        // Check if user details did not change
        if (
            $data['fullname'] === $_SESSION['logged_in_user']['fullname'] &&
            $data['email_address'] === $_SESSION['logged_in_user']['email_address']
        ) {
            $_SESSION['flash_message'] = "No changes detected in account settings.";
            return;
        }

        // Check for email uniqueness
        foreach ($_SESSION['users_table'] as $user) {
            if (
                $user['email_address'] === $data['email_address'] &&
                $user['email_address'] !== $_SESSION['logged_in_user']['email_address']
            ) {
                $_SESSION['flash_message'] = "The email address is already in use by another account.";
                return;
            }
        }

        // Update session data
        $_SESSION['logged_in_user']['fullname'] = $data['fullname'];
        $_SESSION['logged_in_user']['email_address'] = $data['email_address'];

        $_SESSION['flash_message'] = "Account settings updated successfully!";
    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}