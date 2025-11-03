<!doctype html>
<html lang="en" data-bs-theme="dark">

<?php

include "./server/initialize.php";

if (!isset($_SESSION['logged_in_user'])) {
    $_SESSION['flash_message'] = "Please log in to access the dashboard.";
    header('Location: ./login');
    exit();
}

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
        <h1 class="fs-3">
            Good afternoon <?= $_SESSION['logged_in_user']['fullname'] ?>!
        </h1>

        <div class="d-flex flex-column align-items-center gap-4 mt-4">
            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#newProjectModal"
                class="btn btn-lg rounded-4 btn-success align-self-start">
                Create New Project
            </a>

            <div class="card w-100 p-4 rounded-4 border border-dashed px-0 pt-2 table-responsive pb-0">
                <table class="table table-striped table-hover">
                    <caption class="ps-4 pt-4">List of created projects</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="ps-4">#</th>
                            <th scope="col">Project Name</th>
                            <th scope="col">Deadline</th>
                            <th scope="col">Contractor</th>
                            <th scope="col">Budget</th>
                            <th scope="col">Project Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if (!empty($_SESSION['projects_table'])) { ?>
                            <?php foreach ($_SESSION['projects_table'] as $index => $project): ?>
                                <tr>
                                    <th scope="row" class="ps-4">
                                        <?= $index + 1 ?>
                                    </th>
                                    <td>
                                        <?= $project["project_name"] ?>
                                    </td>
                                    <td>
                                        <?= $project["project_deadline"] ?: "-- / --" ?>
                                    </td>
                                    <td>
                                        <?= $project["contractor_name"] ?: "-- / --" ?>
                                    </td>
                                    <td>
                                        <?php if (is_numeric($project["project_budget"])) { ?>
                                            $ <?= number_format($project["project_budget"], 2) ?>
                                        <?php } else { ?>
                                            -- / --
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <form action="" method="post">
                                                <input type="hidden" name="project_id" value="<?= $project["project_id"] ?>">
                                                <button name="delete_project" class="badge text-bg-danger border border-danger"
                                                    onclick="return confirm('This action is irreversible! Please proceed with caution.');">
                                                    <i class="bi-trash"></i>
                                                </button>
                                            </form>
                                            <a href="./manage-project?project_id=<?= $project["project_id"] ?>" target="_blank"
                                                class="badge text-bg-success border border-success d-flex align-items-center gap-1">
                                                <i class="bi-gear"></i>Manage
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } else { ?>
                            <tr>
                                <th scope="row" colspan="6" class="ps-4">No records found!</th>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- New Project Modal -->
        <div class="modal fade" id="newProjectModal" tabindex="-1" aria-labelledby="newProjectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4">
                    <div class="modal-header border-0 border-bottom border-dashed">
                        <h1 class="modal-title fs-5" id="newProjectModalLabel">Create New Project</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="projectName" class="form-label">Project Name</label>
                                    <input type="text" name="project_name"
                                        class="form-control form-control-lg rounded-4 border border-dashed"
                                        placeholder="Enter project name">
                                </div>
                                <div class="col-md-6">
                                    <label for="projectDeadline" class="form-label">Project Deadline</label>
                                    <input type="datetime-local" name="project_deadline"
                                        class="form-control form-control-lg rounded-4 border border-dashed"
                                        placeholder="Enter project deadline">
                                </div>
                                <div class="col-md-12">
                                    <label for="projectDescription" class="form-label">Project Description</label>
                                    <textarea name="project_description"
                                        class="form-control form-control-lg rounded-4 border border-dashed" rows="3"
                                        placeholder="Enter project description"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <span class="badge p-2 text-success border border-success border-dashed">Contractor
                                        Details</span>
                                </div>
                                <div class="col-md-4">
                                    <label for="contractor" class="form-label">Full Name</label>
                                    <input type="text" name="contractor_name"
                                        class="form-control form-control-lg rounded-4 border border-dashed"
                                        placeholder="e.g. John Doe">
                                </div>
                                <div class="col-md-4">
                                    <label for="contractorEmail" class="form-label">Email Address</label>
                                    <input type="email" name="contractor_email"
                                        class="form-control form-control-lg rounded-4 border border-dashed"
                                        placeholder="e.g. contractor@example.com">
                                </div>
                                <div class="col-md-4">
                                    <label for="budget" class="form-label">Project Budget ($)</label>
                                    <input type="number" name="project_budget"
                                        class="form-control form-control-lg rounded-4 border border-dashed"
                                        placeholder="$0.00 USD">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 border-top border-dashed d-flex align-items-center">
                            <button type="button" class="btn btn-lg rounded-4 btn-secondary me-auto"
                                data-bs-dismiss="modal">Cancel</button>
                            <button name="create_project" class="btn btn-lg rounded-4 btn-success">Create
                                Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require_once "./includes/footer.php" ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>