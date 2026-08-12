<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();

$payments = fetch_all('SELECT * FROM payment ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments — BlueBird Admin</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="admin-page">
    <div class="searchsection">
        <div class="search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="search_bar" placeholder="Search payments..." onkeyup="searchFun()">
        </div>
    </div>

    <div class="content-wrap">
        <div class="roombooktable">
            <table id="table-data">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Room</th>
                        <th>Bed</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Days</th>
                        <th>Rooms</th>
                        <th>Meal</th>
                        <th>Room rent</th>
                        <th>Bed rent</th>
                        <th>Meals</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($payments === []): ?>
                        <tr><td colspan="14" class="empty-state">No payments recorded yet.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($payments as $res): ?>
                        <tr>
                            <td><?php echo e($res['id']); ?></td>
                            <td><?php echo e($res['Name']); ?></td>
                            <td><?php echo e($res['RoomType']); ?></td>
                            <td><?php echo e($res['Bed']); ?></td>
                            <td><?php echo e($res['cin']); ?></td>
                            <td><?php echo e($res['cout']); ?></td>
                            <td><?php echo e($res['noofdays']); ?></td>
                            <td><?php echo e($res['NoofRoom']); ?></td>
                            <td><?php echo e($res['meal']); ?></td>
                            <td>&#8377; <?php echo e(number_format((float) $res['roomtotal'], 2)); ?></td>
                            <td>&#8377; <?php echo e(number_format((float) $res['bedtotal'], 2)); ?></td>
                            <td>&#8377; <?php echo e(number_format((float) $res['mealtotal'], 2)); ?></td>
                            <td class="total-cell">&#8377; <?php echo e(number_format((float) $res['finaltotal'], 2)); ?></td>
                            <td class="action">
                                <a class="btn-mini btn-mini-edit" href="./invoiceprint.php?id=<?php echo e($res['id']); ?>" title="Print invoice">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <form action="./paymantdelete.php" method="POST" class="inline-form">
                                    <input type="hidden" name="id" value="<?php echo e($res['id']); ?>">
                                    <button class="btn-mini btn-mini-danger" title="Delete" onclick="return confirm('Delete this payment?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="./javascript/admin.js"></script>
</body>

</html>