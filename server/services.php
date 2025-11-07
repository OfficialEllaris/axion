<?php

function onboardUser($data, $conn)
{
    // Validate required fields
    if (!empty($data['fullname']) && !empty($data['email_address']) && !empty($data['password'])) {

        try {
            // First: check if user already exists by email
            $stmt = $conn->prepare(
                "SELECT * FROM users 
                WHERE email_address = :email_address 
                LIMIT 1"
            );
            $stmt->execute([
                ':email_address' => $data['email_address']
            ]);

            $found_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($found_user) {
                $_SESSION['flash_message'] = "An account with this email address already exists!";
                return;
            }

            // Hash the password with bcrypt
            $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);

            // Insert new user into the database
            $insert = $conn->prepare(
                "INSERT INTO users (fullname, email_address, password, created_at, updated_at)
                VALUES (:fullname, :email_address, :password, :created_at, :updated_at)"
            );
            $insert->execute([
                ':fullname' => $data['fullname'],
                ':email_address' => $data['email_address'],
                ':password' => $hashed_password,
                ':created_at' => date('Y-m-d H:i:s'),
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);

            $_SESSION['flash_message'] = "Account created successfully!";

        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
        }
    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}

function loginUser($data, $conn)
{
    // Validate required fields
    if (!empty($data['email_address']) && !empty($data['password'])) {

        try {
            // Find user by email in the database
            $stmt = $conn->prepare(
                "SELECT * FROM users 
                WHERE email_address = :email_address 
                LIMIT 1"
            );
            $stmt->execute([
                ':email_address' => $data['email_address']
            ]);
            $found_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($found_user) {
                // Verify the password
                if (password_verify($data['password'], $found_user['password'])) {
                    $fullname = htmlspecialchars($found_user['fullname']);
                    $_SESSION['logged_in_user'] = $found_user['id'];
                    $_SESSION['flash_message'] = "Welcome back, $fullname!";
                    header('Location: ./dashboard');
                    exit();
                } else {
                    $_SESSION['flash_message'] = "Invalid account credentials.";
                }
            } else {
                $_SESSION['flash_message'] = "The provided email address does not exist in our system.";
            }
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
        }

    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}

function fetchUserData($conn)
{
    if (empty($_SESSION['logged_in_user'])) {
        return null;
    }

    $user_id = $_SESSION['logged_in_user'];

    try {
        $stmt = $conn->prepare(
            "SELECT * FROM users 
            WHERE id = :user_id LIMIT 1"
        );
        $stmt->execute([
            ':user_id' => $user_id
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;

    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
        return null;
    }
}

function fetchUserProjects($conn, $user_id)
{
    try {
        $stmt = $conn->prepare("
            SELECT * FROM projects 
            WHERE owner_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([
            ':user_id' => $user_id
        ]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $projects ?: []; // return empty array if none found

    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
        return [];
    }
}

function resetPassword($data, $conn)
{
    // Check required fields
    if (!empty($data['email_address']) && !empty($data['password']) && !empty($data['password_confirmation'])) {

        // Check if passwords match
        if ($data['password'] !== $data['password_confirmation']) {
            $_SESSION['flash_message'] = "Passwords do not match.";
            return;
        }

        try {
            // Check if user exists
            $stmt = $conn->prepare(
                "SELECT * FROM users 
                WHERE email_address = :email_address 
                LIMIT 1"
            );
            $stmt->execute([
                ':email_address' => $data['email_address']
            ]);
            $found_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($found_user) {
                $now = date('Y-m-d H:i:s');

                // Hash the new password
                $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);

                // Update the password in the database
                $update = $conn->prepare(
                    "UPDATE users 
                    SET password = :password, updated_at = :updated_at 
                    WHERE email_address = :email_address"
                );
                $update->execute([
                    ':password' => $hashed_password,
                    ':updated_at' => $now,
                    ':email_address' => $data['email_address']
                ]);

                $_SESSION['flash_message'] = "Account recovery successful!";
                header('Location: ./login');
                exit();

            } else {
                $_SESSION['flash_message'] = "The provided email address does not exist in our system.";
            }

        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
        }

    } else {
        $_SESSION['flash_message'] = "All fields are required.";
    }
}

function updateProfile($data, $conn)
{
    // Fetch current user from database
    $current_user = fetchUserData($conn);

    // Validate required fields
    if (empty($data['fullname']) || empty($data['email_address'])) {
        $_SESSION['flash_message'] = "All fields are required.";
        return;
    }

    // Check if details didn't change
    if (
        $data['fullname'] === $current_user['fullname'] &&
        $data['email_address'] === $current_user['email_address']
    ) {
        $_SESSION['flash_message'] = "No changes detected in account settings.";
        return;
    }

    try {
        // Check if email already exists for another user
        $stmt = $conn->prepare(
            "SELECT 1 FROM users 
            WHERE email_address = :email_address AND id != :user_id
            LIMIT 1"
        );
        $stmt->execute([
            ':email_address' => $data['email_address'],
            ':user_id' => $current_user['id']
        ]);

        if ($stmt->fetch()) {
            $_SESSION['flash_message'] = "That email address is already in use by another account.";
            return;
        }

        // Update user record
        $update = $conn->prepare(
            "UPDATE users 
            SET fullname = :fullname, 
                email_address = :email_address, 
                updated_at = :updated_at
            WHERE id = :user_id"
        );
        $update->execute([
            ':fullname' => $data['fullname'],
            ':email_address' => $data['email_address'],
            ':updated_at' => date('Y-m-d H:i:s'),
            ':user_id' => $current_user['id']
        ]);

        $_SESSION['flash_message'] = "Account settings updated successfully!";

    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
    }
}

function updatePassword($data, $conn)
{
    // Fetch current user
    $current_user = fetchUserData($conn);

    if (!$current_user) {
        $_SESSION['flash_message'] = "User not logged in or not found.";
        return;
    }

    // Validate required fields
    if (
        empty($data['current_password']) ||
        empty($data['new_password']) ||
        empty($data['password_confirmation'])
    ) {
        $_SESSION['flash_message'] = "All fields are required.";
        return;
    }

    // Check password confirmation
    if ($data['new_password'] !== $data['password_confirmation']) {
        $_SESSION['flash_message'] = "New password and confirmation do not match.";
        return;
    }

    // Verify current password
    if (!password_verify($data['current_password'], $current_user['password'])) {
        $_SESSION['flash_message'] = "Current password is incorrect.";
        return;
    }

    // Ensure new password isn't same as current
    if (password_verify($data['new_password'], $current_user['password'])) {
        $_SESSION['flash_message'] = "New password cannot be the same as the current password.";
        return;
    }

    try {
        $update = $conn->prepare("
            UPDATE users 
            SET password = :password, updated_at = :updated_at 
            WHERE id = :user_id
        ");
        $update->execute([
            ':password' => password_hash($data['new_password'], PASSWORD_BCRYPT),
            ':updated_at' => date('Y-m-d H:i:s'),
            ':user_id' => $current_user['id']
        ]);

        $_SESSION['flash_message'] = "Password updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
    }
}

function createProject($data, $conn)
{
    if (!empty($data['title']) && !empty($data['description']) && !empty($data['deadline']) && !empty($data['budget'])) {

        $owner_id = $_SESSION['logged_in_user']; // Assuming your users table uses 'id'
        $now = date('Y-m-d H:i:s');

        $stmt = $conn->prepare(
            "INSERT INTO projects (title, deadline, description, budget, created_at, updated_at, owner_id, contractor_name, contractor_email)
            VALUES (:title, :deadline, :description, :budget, :created_at, :updated_at, :owner_id, :contractor_name, :contractor_email)"
        );

        $stmt->execute([
            ':title' => $data['title'],
            ':deadline' => $data['deadline'],
            ':description' => $data['description'],
            ':budget' => $data['budget'],
            ':created_at' => $now,
            ':updated_at' => $now,
            ':owner_id' => $owner_id,
            ':contractor_name' => $data['contractor_name'] ?? null,
            ':contractor_email' => $data['contractor_email'] ?? null
        ]);

        $_SESSION['flash_message'] = "Project created successfully!";
    } else {
        $_SESSION['flash_message'] = "Project title and description are required.";
    }
}

function deleteProject($data, $conn)
{
    $owner_id = $_SESSION['logged_in_user'];

    $stmt = $conn->prepare(
        "DELETE FROM projects
        WHERE id = :project_id AND owner_id = :owner_id"
    );
    $stmt->execute([
        ':project_id' => $data['project_id'],
        ':owner_id' => $owner_id
    ]);

    if (!$stmt->rowCount() > 0) {
        $_SESSION['flash_message'] = "Project not found or unauthorized action.";
    }

    $_SESSION['flash_message'] = "Project deleted successfully!";
}

function fetchProjectData($data, $conn): array
{
    $owner_id = $_SESSION['logged_in_user'];

    $stmt = $conn->prepare(
        "SELECT * FROM projects 
        WHERE id = :project_id AND owner_id = :owner_id"
    );
    $stmt->execute([
        ':project_id' => $data['project_id'],
        ':owner_id' => $owner_id
    ]);

    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        $_SESSION['flash_message'] = "Project not found or unauthorized access.";
        header('Location: ./dashboard');
        exit();
    }

    return $project;
}

function modifyProject($data, $conn)
{
    if (empty($data['title']) || empty($data['description']) || empty($data['deadline']) || empty($data['budget'])) {
        $_SESSION['flash_message'] = "Project title and description are required.";
        return;
    }

    $owner_id = $_SESSION['logged_in_user'];
    $now = date('Y-m-d H:i:s');

    $stmt = $conn->prepare(
        "UPDATE projects
        SET  title = :title, deadline = :deadline, description = :description, budget = :budget, contractor_name = :contractor_name, contractor_email = :contractor_email, updated_at = :updated_at 
        WHERE id = :project_id AND owner_id = :owner_id"
    );

    $stmt->execute([
        ':title' => $data['title'],
        ':deadline' => $data['deadline'],
        ':description' => $data['description'],
        ':budget' => $data['budget'],
        ':contractor_name' => $data['contractor_name'] ?? null,
        ':contractor_email' => $data['contractor_email'] ?? null,
        ':updated_at' => $now,
        ':project_id' => $data['project_id'],
        ':owner_id' => $owner_id
    ]);

    if (!$stmt->rowCount() > 0) {
        $_SESSION['flash_message'] = "Project not found or no changes made.";
    }

    $_SESSION['flash_message'] = "Project updated successfully!";
}
