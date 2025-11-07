<!doctype html>
<html lang="en" data-bs-theme="dark">

<?php

include "./server/initialize.php";

if (!isset($_SESSION['logged_in_user'])) {
    $_SESSION['flash_message'] = "Please log in to access the dashboard.";
    header('Location: ./login');
    exit();
}

$user = fetchUserData($dbConn);

?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Axion - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./assets/styles.css">
</head>

<body class="d-flex flex-column min-vh-100 container">

    <?php require_once "./includes/navbar.php" ?>

    <!-- Main content -->
    <main class="flex-grow-1 d-flex flex-column gap-5">
        <?php
        if (isset($_SESSION['flash_message'])) {
            ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                <strong>Feedback!</strong> <?= $_SESSION['flash_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            // Unset after displaying once
            unset($_SESSION['flash_message']);
        }
        ?>

        <form action="" method="post" class="row g-3">
            <h1 class="fs-3">
                Update Account Profile
            </h1>
            <div class="col-md-6">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control form-control-lg rounded-4" name="fullname"
                    value="<?= $user['fullname'] ?>" placeholder="e.g. John Doe">
            </div>
            <div class="col-md-6">
                <label for="email_address" class="form-label">Email address</label>
                <input type="email" class="form-control form-control-lg rounded-4" name="email_address"
                    value="<?= $user['email_address'] ?>" placeholder="e.g. john@example.com">
            </div>
            <div class="col-12">
                <button type="submit" name="update_profile" class="btn btn-lg btn-success rounded-4 w-auto">
                    Update Profile
                </button>
            </div>
        </form>

        <form action="" method="post" class="row g-3">
            <h1 class="fs-3">
                Manage Account Security
            </h1>
            <div class="col-md-4">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control form-control-lg rounded-4" name="current_password"
                    placeholder="********">
            </div>
            <div class="col-md-4">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control form-control-lg rounded-4" name="new_password"
                    placeholder="********">
            </div>
            <div class="col-md-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control form-control-lg rounded-4" name="password_confirmation"
                    placeholder="********">
            </div>
            <div class="col-12">
                <button type="submit" name="update_password" class="btn btn-lg btn-success rounded-4 w-auto">
                    Update Password
                </button>
            </div>
        </form>
    </main>

    <?php require_once "./includes/footer.php" ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>