<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();

/**
 * Handle adding a booking from the admin panel.
 */
if (isset($_POST['guestdetailsubmit'])) {
    $fields = ['Name', 'Email', 'Country', 'Phone', 'RoomType', 'Bed', 'NoofRoom', 'Meal', 'cin', 'cout'];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = trim($_POST[$field] ?? '');
    }

    if ($data['Name'] === '' || $data['Email'] === '' || $data['Country'] === '' || $data['cin'] === '' || $data['cout'] === '') {
        echo "<script>swal({ title: 'Please fill in the proper details', icon: 'error' });</script>";
    } else {
        try {
            $sql = "INSERT INTO roombook
                        (Name, Email, Country, Phone, RoomType, Bed, NoofRoom, Meal, cin, cout, stat, nodays)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'NotConfirm', DATEDIFF(?, ?))";
            insert_row($sql, 'ssssssssssss', [
                $data['Name'], $data['Email'], $data['Country'], $data['Phone'],
                $data['RoomType'], $data['Bed'], $data['NoofRoom'], $data['Meal'],
                $data['cin'], $data['cout'], $data['cout'], $data['cin'],
            ]);
            echo "<script>swal({ title: 'Reservation added successfully', icon: 'success' });</script>";
        } catch (RuntimeException $e) {
            echo "<script>swal({ title: 'Something went wrong', icon: 'error' });</script>";
        }
    }
}

$bookings = fetch_all('SELECT * FROM roombook ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking — BlueBird Admin</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body class="admin-page">
    <div class="searchsection">
        <div class="search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="search_bar" placeholder="Search bookings..." onkeyup="searchFun()">
        </div>
        <div class="toolbar-actions">
            <button class="btn-primary-outline" onclick="adduseropen()"><i class="fa-solid fa-plus"></i> Add booking</button>
            <form action="./exportdata.php" method="post">
                <button class="btn-export" name="exportexcel" type="submit" title="Export to Excel">
                    <i class="fa-solid fa-file-arrow-down"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="content-wrap">
        <div class="roombooktable">
            <table id="table-data">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Country</th>
                        <th>Phone</th>
                        <th>Room</th>
                        <th>Bed</th>
                        <th>Meal</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings === []): ?>
                        <tr><td colspan="12" class="empty-state">No bookings yet.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($bookings as $res): ?>
                        <tr>
                            <td><?php echo e($res['id']); ?></td>
                            <td><?php echo e($res['Name']); ?></td>
                            <td><?php echo e($res['Country']); ?></td>
                            <td><?php echo e($res['Phone']); ?></td>
                            <td><?php echo e($res['RoomType']); ?></td>
                            <td><?php echo e($res['Bed']); ?></td>
                            <td><?php echo e($res['Meal']); ?></td>
                            <td><?php echo e($res['cin']); ?></td>
                            <td><?php echo e($res['cout']); ?></td>
                            <td><?php echo e($res['nodays']); ?></td>
                            <td>
                                <span class="badge <?php echo $res['stat'] === 'Confirm' ? 'badge-confirm' : 'badge-pending'; ?>">
                                    <?php echo e($res['stat']); ?>
                                </span>
                            </td>
                            <td class="action">
                                <?php if ($res['stat'] !== 'Confirm'): ?>
                                    <form action="./roomconfirm.php" method="POST" class="inline-form">
                                        <input type="hidden" name="id" value="<?php echo e($res['id']); ?>">
                                        <button class="btn-mini btn-mini-ok" title="Confirm" onclick="return confirm('Confirm this booking?')">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a class="btn-mini btn-mini-edit" href="./roombookedit.php?id=<?php echo e($res['id']); ?>" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="./roombookdelete.php" method="POST" class="inline-form">
                                    <input type="hidden" name="id" value="<?php echo e($res['id']); ?>">
                                    <button class="btn-mini btn-mini-danger" title="Delete" onclick="return confirm('Delete this booking?')">
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

    <!-- Add booking modal -->
    <div id="guestdetailpanel">
        <div class="guestdetailpanelform">
            <form action="" method="POST" class="reservation-form">
                <div class="head">
                    <h3>New Reservation</h3>
                    <i class="fa-solid fa-circle-xmark" onclick="adduserclose()"></i>
                </div>
                <div class="middle">
                    <div class="guestinfo">
                        <h4>Guest information</h4>
                        <div class="field">
                            <label>Full name</label>
                            <input type="text" name="Name" placeholder="Enter full name" required>
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="Email" placeholder="you@example.com" required>
                        </div>
                        <div class="field">
                            <label>Country</label>
                            <select name="Country" class="selectinput" required>
                                <option value="" selected disabled>Select your country</option>
                                <?php foreach (countries() as $country): ?>
                                    <option value="<?php echo e($country); ?>"><?php echo e($country); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <input type="text" name="Phone" placeholder="Enter phone number" required>
                        </div>
                    </div>

                    <div class="line"></div>

                    <div class="reservationinfo">
                        <h4>Reservation information</h4>
                        <div class="field">
                            <label>Room type</label>
                            <select name="RoomType" class="selectinput" required>
                                <option value="" selected disabled>Type of room</option>
                                <option value="Superior Room">Superior Room</option>
                                <option value="Deluxe Room">Deluxe Room</option>
                                <option value="Guest House">Guest House</option>
                                <option value="Single Room">Single Room</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Bedding type</label>
                            <select name="Bed" class="selectinput" required>
                                <option value="" selected disabled>Bedding type</option>
                                <option value="Single">Single</option>
                                <option value="Double">Double</option>
                                <option value="Triple">Triple</option>
                                <option value="Quad">Quad</option>
                                <option value="None">None</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Meal plan</label>
                            <select name="Meal" class="selectinput" required>
                                <option value="" selected disabled>Meal</option>
                                <option value="Room only">Room only</option>
                                <option value="Breakfast">Breakfast</option>
                                <option value="Half Board">Half Board</option>
                                <option value="Full Board">Full Board</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Number of rooms</label>
                            <select name="NoofRoom" class="selectinput">
                                <option value="1" selected>1</option>
                            </select>
                        </div>
                        <div class="datesection">
                            <div class="field">
                                <label>Check-in</label>
                                <input type="date" name="cin" required>
                            </div>
                            <div class="field">
                                <label>Check-out</label>
                                <input type="date" name="cout" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <button type="submit" name="guestdetailsubmit" class="btn btn-submit">Add reservation</button>
                </div>
            </form>
        </div>
    </div>

    <script src="./javascript/admin.js"></script>
    <?php render_flash_alerts(); ?>
</body>

</html>