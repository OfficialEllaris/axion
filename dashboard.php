<!doctype html>
<html lang="en" data-bs-theme="dark">

<?php include "./server/initialize.php"; ?>

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
    <main class="flex-grow-1 d-flex flex-column">
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
        <h1 class="fs-3">Good afternoon <?= $_COOKIE['fullname'] ?>!</h1>
    </main>

    <?php require_once "./includes/footer.php" ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>