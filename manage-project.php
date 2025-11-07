<!doctype html>
<html lang="en" data-bs-theme="dark">

<?php

include "./server/initialize.php";

if (!isset($_SESSION['logged_in_user'])) {
    $_SESSION['flash_message'] = "Please log in to access the dashboard.";
    header('Location: ./login');
    exit();
}

// Fetch project if ID is present
if (isset($_GET['project_id']) && !empty($_GET['project_id'])) {
    $project_data = fetchProjectData($_GET, $dbConn);
} else {
    $_SESSION['flash_message'] = "Project ID unavailable!";
    header('Location: ./dashboard');
    exit();
}

?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Axion - Manage Project</title>
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

        <form action="" method="post" class="d-flex flex-column gap-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="projectName" class="form-label">Project Name</label>
                    <input type="text" name="title" value="<?= $project_data['title'] ?>"
                        class="form-control form-control-lg rounded-4 border border-dashed"
                        placeholder="Enter project name">
                </div>
                <div class="col-md-6">
                    <label for="projectDeadline" class="form-label">Project Deadline</label>
                    <input type="datetime-local" name="deadline" value="<?= $project_data['deadline'] ?>"
                        class="form-control form-control-lg rounded-4 border border-dashed"
                        placeholder="Enter project deadline">
                </div>
                <div class="col-md-12">
                    <label for="projectDescription" class="form-label">Project Description</label>
                    <textarea name="description" class="form-control form-control-lg rounded-4 border border-dashed"
                        rows="3" placeholder="Enter project description"><?= $project_data['description'] ?></textarea>
                </div>
                <div class="col-md-12">
                    <span class="badge p-2 text-success border border-success border-dashed">Contractor
                        Details</span>
                </div>
                <div class="col-md-4">
                    <label for="contractor" class="form-label">Full Name</label>
                    <input type="text" name="contractor_name"
                        class="form-control form-control-lg rounded-4 border border-dashed"
                        value="<?= $project_data['contractor_name'] ?>" placeholder="e.g. John Doe">
                </div>
                <div class="col-md-4">
                    <label for="contractorEmail" class="form-label">Email Address</label>
                    <input type="email" name="contractor_email" value="<?= $project_data['contractor_email'] ?>"
                        class="form-control form-control-lg rounded-4 border border-dashed"
                        placeholder="e.g. contractor@example.com">
                </div>
                <div class="col-md-4">
                    <label for="budget" class="form-label">Project Budget ($)</label>
                    <input type="number" name="budget" value="<?= $project_data['budget'] ?>"
                        class="form-control form-control-lg rounded-4 border border-dashed" placeholder="$0.00 USD">
                </div>
            </div>
            <div class="d-flex align-items-center">
                <input type="hidden" name="project_id" value="<?= $project_data['id'] ?>">
                <button type="reset" class="btn btn-lg rounded-4 btn-secondary me-auto">Cancel</button>
                <button name="modify_project" class="btn btn-lg rounded-4 btn-success">Save Changes</button>
            </div>
        </form>
    </main>

    <?php require_once "./includes/footer.php" ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>