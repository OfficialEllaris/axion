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
                    <a class="nav-link active" aria-current="page" href="./about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./pricing">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./contact">Contact</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <?php if (isset($_SESSION['logged_in_user'])) { ?>
                    <a class="btn btn-outline-success rounded-3 border border-success border-dashed"
                        href="./settings">Settings</a>
                    <form action="" method="POST">
                        <button class="btn btn-success rounded-3" name="logout_user">Logout</button>
                    </form>
                <?php } else { ?>
                    <a href="./login" class="btn btn-success rounded-3">Login</a>
                    <a href="./onboarding"
                        class="btn btn-outline-success rounded-3 border border-success border-dashed">Onboarding</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<!-- And url is not /dashboard -->
<?php if (isset($_SESSION['logged_in_user']) && $_SERVER['REQUEST_URI'] !== '/dashboard') { ?>
    <div class="d-flex align-items-center">
        <a class="btn btn-outline-success rounded-3 border border-success border-dashed mb-4" href="./dashboard">
            Goto Dashboard
        </a>
    </div>
<?php } ?>