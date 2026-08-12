<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlueBird — Admin Panel</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div id="mobileview">
        <div class="mobile-note">
            <i class="fa-solid fa-mobile-screen"></i>
            <h5>The admin panel works best on a desktop or landscape tablet.</h5>
        </div>
    </div>

    <nav class="topbar">
        <div class="topbar-left">
            <img src="../image/bluebirdlogo.png" alt="Blue Bird logo">
            <span>BLUEBIRD · ADMIN</span>
        </div>
        <div class="topbar-right">
            <span class="admin-greeting"><i class="fa-regular fa-user"></i> <?php echo e($_SESSION['username'] ?? 'Admin'); ?></span>
            <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
        </div>
    </nav>

    <div class="app-body">
        <nav class="sidenav">
            <ul>
                <li class="pagebtn active" data-index="0">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard
                </li>
                <li class="pagebtn" data-index="1">
                    <i class="fa-solid fa-bookmark"></i> Room Booking
                </li>
                <li class="pagebtn" data-index="2">
                    <i class="fa-solid fa-wallet"></i> Payment
                </li>
                <li class="pagebtn" data-index="3">
                    <i class="fa-solid fa-bed"></i> Rooms
                </li>
                <li class="pagebtn" data-index="4">
                    <i class="fa-solid fa-people-group"></i> Staff
                </li>
            </ul>
        </nav>

        <main class="mainscreen">
            <iframe class="frames active" data-frame="0" src="./dashboard.php" frameborder="0" title="Dashboard"></iframe>
            <iframe class="frames" data-frame="1" src="./roombook.php" frameborder="0" title="Room Booking"></iframe>
            <iframe class="frames" data-frame="2" src="./payment.php" frameborder="0" title="Payments"></iframe>
            <iframe class="frames" data-frame="3" src="./room.php" frameborder="0" title="Rooms"></iframe>
            <iframe class="frames" data-frame="4" src="./staff.php" frameborder="0" title="Staff"></iframe>
        </main>
    </div>

    <script src="./javascript/admin.js"></script>
</body>

</html>