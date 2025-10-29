<!doctype html>
<html lang="en" data-bs-theme="dark">

<?php include "./server/initialize.php"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Axion - Onboarding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./assets/styles.css">
</head>

<body class="d-flex flex-column min-vh-100 container">

    <?php require_once "./includes/navbar.php" ?>

    <!-- Main content -->
    <main class="flex-grow-1 d-flex flex-column">
        <h1>Onboarding.</h1>

        <form method="POST" action="./onboarding.php" class="row g-3">
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
            <div class="col-md-4">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control form-control-lg rounded-4" name="fullname"
                    placeholder="e.g John Doe">
            </div>
            <div class="col-md-4">
                <label for="email_address" class="form-label">Email Address</label>
                <input type="email" class="form-control form-control-lg rounded-4" name="email_address"
                    placeholder="e.g john@example.com">
            </div>
            <div class="col-md-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control form-control-lg rounded-4" name="password"
                    placeholder="********">
            </div>
            <div class="col-12">
                <button type="submit" name="onboard_user" class="btn btn-lg btn-success rounded-4 w-auto">
                    Get Started
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