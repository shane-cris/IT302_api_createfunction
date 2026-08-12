<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/includes/helpers.php';

/**
 * Handle the "log in as a customer" form.
 */
if (isset($_POST['user_login_submit'])) {
    $email = trim($_POST['Email'] ?? '');
    $password = $_POST['Password'] ?? '';

    $user = fetch_one("SELECT * FROM signup WHERE Email = ?", 's', [$email]);

    if ($user !== null && verify_password($password, $user['Password'])) {
        session_regenerate_id(true);
        $_SESSION['usermail'] = $user['Email'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role'] = 'user';
        redirect('home.php');
    }
    echo "<script>swal({ title: 'Invalid email or password', icon: 'error' });</script>";
}

/**
 * Handle the "log in as a staff member" form.
 */
if (isset($_POST['Emp_login_submit'])) {
    $email = trim($_POST['Emp_Email'] ?? '');
    $password = $_POST['Emp_Password'] ?? '';

    $staff = fetch_one("SELECT * FROM emp_login WHERE Emp_Email = ?", 's', [$email]);

    if ($staff !== null && verify_password($password, $staff['Emp_Password'])) {
        session_regenerate_id(true);
        $_SESSION['usermail'] = $staff['Emp_Email'];
        $_SESSION['username'] = $staff['Emp_Email'];
        $_SESSION['role'] = 'staff';
        redirect('admin/admin.php');
    }
    echo "<script>swal({ title: 'Invalid email or password', icon: 'error' });</script>";
}

/**
 * Handle the sign-up form.
 */
if (isset($_POST['user_signup_submit'])) {
    $username = trim($_POST['Username'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $password = $_POST['Password'] ?? '';
    $confirm = $_POST['CPassword'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        echo "<script>swal({ title: 'Please fill in all the details', icon: 'error' });</script>";
    } elseif ($password !== $confirm) {
        echo "<script>swal({ title: 'Passwords do not match', icon: 'error' });</script>";
    } else {
        $existing = fetch_one("SELECT UserID FROM signup WHERE Email = ?", 's', [$email]);

        if ($existing !== null) {
            echo "<script>swal({ title: 'Email is already registered', icon: 'error' });</script>";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $id = insert_row(
                "INSERT INTO signup (Username, Email, Password) VALUES (?, ?, ?)",
                'sss',
                [$username, $email, $hash]
            );

            if ($id > 0) {
                session_regenerate_id(true);
                $_SESSION['usermail'] = $email;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                redirect('home.php');
            }
            echo "<script>swal({ title: 'Something went wrong, please try again', icon: 'error' });</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Blue Bird — Sign in</title>
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>
    <div class="split left">
        <div id="authCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./image/hotel1.jpg" alt="Hotel Blue Bird">
                </div>
                <div class="carousel-item">
                    <img src="./image/hotel2.jpg" alt="Hotel Blue Bird">
                </div>
                <div class="carousel-item">
                    <img src="./image/hotel3.jpg" alt="Hotel Blue Bird">
                </div>
                <div class="carousel-item">
                    <img src="./image/hotel4.jpg" alt="Hotel Blue Bird">
                </div>
            </div>
        </div>
        <div class="left-overlay">
            <div class="brand-lockup">
                <h1 class="brand-name">Hotel Blue Bird</h1>
                <p class="brand-tagline">A stay that feels like heaven on earth</p>
            </div>
        </div>
    </div>

    <main class="split right">
        <div class="auth-panel">
            <div class="auth-logo">
                <img src="./image/bluebirdlogo.png" alt="Blue Bird logo">
                <span>BLUEBIRD</span>
            </div>

            <div class="auth-card">
                <!-- Login -->
                <section id="Log_in">
                    <h2 class="auth-title">Welcome back</h2>
                    <p class="auth-subtitle">Sign in to continue</p>

                    <div class="role_btn">
                        <div class="btns active" data-index="0">User</div>
                        <div class="btns" data-index="1">Staff</div>
                    </div>

                    <form id="userlogin" class="authsection active" action="" method="POST">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="Username" placeholder=" " autocomplete="username">
                            <label for="Username">Username</label>
                        </div>
                        <div class="form-floating">
                            <input type="email" class="form-control" name="Email" placeholder=" " autocomplete="email">
                            <label for="Email">Email</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" name="Password" placeholder=" " autocomplete="current-password">
                            <label for="Password">Password</label>
                        </div>
                        <button type="submit" name="user_login_submit" class="auth_btn">Log in</button>
                        <p class="footer_line">
                            Don't have an account?
                            <span class="page_move_btn" onclick="signuppage()">Sign up</span>
                        </p>
                    </form>

                    <form id="employeelogin" class="authsection" action="" method="POST">
                        <div class="form-floating">
                            <input type="email" class="form-control" name="Emp_Email" placeholder=" " autocomplete="email">
                            <label for="Emp_Email">Email</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" name="Emp_Password" placeholder=" " autocomplete="current-password">
                            <label for="Emp_Password">Password</label>
                        </div>
                        <button type="submit" name="Emp_login_submit" class="auth_btn">Log in as Staff</button>
                    </form>
                </section>

                <!-- Sign up -->
                <section id="sign_up">
                    <h2 class="auth-title">Create account</h2>
                    <p class="auth-subtitle">Join Hotel Blue Bird today</p>

                    <form id="usersignup" action="" method="POST">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="Username" placeholder=" " autocomplete="username">
                            <label for="Username">Full name</label>
                        </div>
                        <div class="form-floating">
                            <input type="email" class="form-control" name="Email" placeholder=" " autocomplete="email">
                            <label for="Email">Email</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" name="Password" placeholder=" " autocomplete="new-password">
                            <label for="Password">Password</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" name="CPassword" placeholder=" " autocomplete="new-password">
                            <label for="CPassword">Confirm password</label>
                        </div>
                        <button type="submit" name="user_signup_submit" class="auth_btn">Sign up</button>
                        <p class="footer_line">
                            Already have an account?
                            <span class="page_move_btn" onclick="loginpage()">Log in</span>
                        </p>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <script src="./javascript/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>