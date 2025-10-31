<nav class="navbar navbar-expand-lg bg-body-tertiary my-4 rounded-4 px-2">
    <div class="container-fluid">
        <a class="navbar-brand fw-medium fs-2 text-success" href="./">Axion.</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./pricing.php">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./contact.php">Contact</a>
                </li>
                <?php if (isset($_SESSION['logged_in_user'])) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="./dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./settings.php">Settings</a>
                    </li>
                <?php } ?>
            </ul>
            <div class="d-flex gap-2">
                <?php if (isset($_SESSION['logged_in_user'])) { ?>
                    <form action="" method="POST">
                        <button class="btn btn-success rounded-3" name="logout_user">Logout</button>
                    </form>
                <?php } else { ?>
                    <a href="./login.php" class="btn btn-success rounded-3">Login</a>
                    <a href="./onboarding.php"
                        class="btn btn-outline-success rounded-3 border border-success border-dashed">Onboarding</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>