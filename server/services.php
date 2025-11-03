<?php

function onboardUser($data)
{
    if (
        !empty($data['fullname']) &&
        !empty($data['email_address']) &&
        !empty($data['password'])
    ) {
        $found_user = null;

        // First: find user by email
        foreach ($_SESSION['users_table'] as $user) {
            if ($user['email_address'] === $data['email_address']) {
                $found_user = $user;
                break;
            }
        }

        if ($found_user) {
            $_SESSION['flash_message'] = "An account with this email address already exists!";
            return;
        }

        // Hash the password with bcrypt
        $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);

        $new_user = [
            'user_id' => uniqid('user_'),
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
                    header('Location: ./dashboard');
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
                header('Location: ./login');
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

function updateProfile($data)
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
        $_SESSION['logged_in_user']['user_id'] = uniqid('user_');
        $_SESSION['logged_in_user']['fullname'] = $data['fullname'];
        $_SESSION['logged_in_user']['email_address'] = $data['email_address'];

        $_SESSION['flash_message'] = "Account settings updated successfully!";
    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}

function updatePassword($data)
{
    if (
        !empty($data['current_password']) &&
        !empty($data['new_password']) &&
        !empty($data['password_confirmation'])
    ) {
        if ($data['new_password'] !== $data['password_confirmation']) {
            $_SESSION['flash_message'] = "New password and confirmation do not match.";
            return;
        }

        // Verify current password
        if (!password_verify($data['current_password'], $_SESSION['logged_in_user']['password'])) {
            $_SESSION['flash_message'] = "Current password is incorrect.";
            return;
        }

        if (password_verify($data['new_password'], $_SESSION['logged_in_user']['password'])) {
            $_SESSION['flash_message'] = "New password cannot be the same as the current password.";
            return;
        }

        // Update password
        $_SESSION['logged_in_user']['password'] = password_hash($data['new_password'], PASSWORD_BCRYPT);

        $_SESSION['flash_message'] = "Password updated successfully!";
    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}

function createProject($data)
{
    if (!empty($data['project_name']) && !empty($data['project_description'])) {
        $new_project = [
            'project_id' => uniqid('proj_'),
            'project_creator' => $_SESSION['logged_in_user']['user_id'],
            'project_name' => $data['project_name'],
            'project_deadline' => $data['project_deadline'],
            'project_description' => $data['project_description'],
            'contractor_name' => $data['contractor_name'],
            'contractor_email' => $data['contractor_email'],
            'project_budget' => $data['project_budget'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['projects_table'][] = $new_project;

        $_SESSION['flash_message'] = "Project created successfully!";
    } else {
        $_SESSION['flash_message'] = "The project name and description fields are required.";
    }
}

function deleteProject($data)
{
    $found = false;

    foreach ($_SESSION['projects_table'] as $project => $project_data) {
        if (
            $project_data['project_id'] === $data['project_id'] &&
            $project_data['project_creator'] === $_SESSION['logged_in_user']['user_id']
        ) {
            unset($_SESSION['projects_table'][$project]);
            $found = true;
            break; // stop after deleting
        }
    }

    // Reindex array after removal
    $_SESSION['projects_table'] = array_values($_SESSION['projects_table']);

    if (!$found) {
        $_SESSION['flash_message'] = "Project not found or unauthorized action.";
        return;
    }

    // Success message
    $_SESSION['flash_message'] = "Project deleted successfully!";
}

function fetchProjectData($data)
{
    $found_project = null;

    foreach ($_SESSION['projects_table'] as $project) {
        if (
            $project['project_id'] === $data['project_id'] &&
            $project['project_creator'] === $_SESSION['logged_in_user']['user_id']
        ) {
            $found_project = $project;
            break;
        }
    }

    if ($found_project) {
        return $found_project;
    } else {
        $_SESSION['flash_message'] = "Project not found or unauthorized access.";
        header('Location: ./dashboard');
        exit();
    }
}

function modifyProject($data)
{
    if (empty($data['project_name']) || empty($data['project_description'])) {
        $_SESSION['flash_message'] = "Project name and description are required.";
        return;
    }

    $found = false;

    foreach ($_SESSION['projects_table'] as &$project) { // use reference to persist changes
        if (
            $project['project_id'] === $data['project_id'] &&
            $project['project_creator'] === $_SESSION['logged_in_user']['user_id']
        ) {
            // Update project fields
            $project['project_name'] = $data['project_name'];
            $project['project_deadline'] = $data['project_deadline'];
            $project['project_description'] = $data['project_description'];
            $project['contractor_name'] = $data['contractor_name'];
            $project['contractor_email'] = $data['contractor_email'];
            $project['project_budget'] = $data['project_budget'];
            $project['updated_at'] = date('Y-m-d H:i:s');

            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['flash_message'] = "Project not found or unauthorized action.";
    }

    // Success feedback
    $_SESSION['flash_message'] = "Project updated successfully!";
}
