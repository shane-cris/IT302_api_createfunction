<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();

$id = (int) ($_GET['id'] ?? 0);
$booking = $id > 0 ? fetch_one('SELECT * FROM roombook WHERE id = ?', 'i', [$id]) : null;

if ($booking === null) {
    redirect('roombook.php');
}

/**
 * Persist the edited booking and keep the payment record in sync.
 */
if (isset($_POST['guestdetailedit'])) {
    $fields = ['Name', 'Email', 'Country', 'Phone', 'RoomType', 'Bed', 'Meal', 'NoofRoom', 'cin', 'cout'];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = trim($_POST[$field] ?? '');
    }

    if ($data['Name'] === '' || $data['Email'] === '' || $data['cin'] === '' || $data['cout'] === '') {
        echo "<script>swal({ title: 'Please fill in the proper details', icon: 'error' });</script>";
    } else {
        // Update the room booking and recompute the number of nights.
        run_mutation(
            "UPDATE roombook SET
                Name = ?, Email = ?, Country = ?, Phone = ?, RoomType = ?, Bed = ?,
                NoofRoom = ?, Meal = ?, cin = ?, cout = ?, nodays = DATEDIFF(?, ?)
             WHERE id = ?",
            'ssssssssssssi',
            [
                $data['Name'], $data['Email'], $data['Country'], $data['Phone'],
                $data['RoomType'], $data['Bed'], $data['NoofRoom'], $data['Meal'],
                $data['cin'], $data['cout'], $data['cout'], $data['cin'], $id,
            ]
        );

        // Recalculate the bill for the payment record using the fresh stay length.
        $prices = booking_prices($data['RoomType'], $data['Bed'], $data['Meal']);
        $fresh = fetch_one('SELECT nodays FROM roombook WHERE id = ?', 'i', [$id]);
        $noOfDays = max(0, (int) ($fresh['nodays'] ?? 0));
        $noOfRooms = (int) $data['NoofRoom'];

        $roomTotal = $prices['room'] * $noOfDays * $noOfRooms;
        $bedTotal = $prices['bed'] * $noOfDays;
        $mealTotal = $prices['meal'] * $noOfDays;
        $finalTotal = $roomTotal + $bedTotal + $mealTotal;

        run_mutation(
            "UPDATE payment SET
                Name = ?, Email = ?, RoomType = ?, Bed = ?, NoofRoom = ?, Meal = ?,
                cin = ?, cout = ?, noofdays = ?, roomtotal = ?, bedtotal = ?, mealtotal = ?, finaltotal = ?
             WHERE id = ?",
            'ssssisssiddddi',
            [
                $data['Name'], $data['Email'], $data['RoomType'], $data['Bed'],
                $noOfRooms, $data['Meal'], $data['cin'], $data['cout'],
                $noOfDays, $roomTotal, $bedTotal, $mealTotal, $finalTotal, $id,
            ]
        );

        flash_alert('Booking updated successfully', 'success');
        redirect('roombook.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation — BlueBird Admin</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body class="admin-page">
    <div id="editpanel">
        <form method="POST" class="guestdetailpanelform reservation-form">
            <div class="head">
                <h3>Edit Reservation</h3>
                <a href="./roombook.php"><i class="fa-solid fa-circle-xmark"></i></a>
            </div>
            <div class="middle">
                <div class="guestinfo">
                    <h4>Guest information</h4>
                    <div class="field">
                        <label>Full name</label>
                        <input type="text" name="Name" value="<?php echo e($booking['Name']); ?>" required>
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="Email" value="<?php echo e($booking['Email']); ?>" required>
                    </div>
                    <div class="field">
                        <label>Country</label>
                        <select name="Country" class="selectinput" required>
                            <?php foreach (countries() as $country): ?>
                                <option value="<?php echo e($country); ?>" <?php echo $booking['Country'] === $country ? 'selected' : ''; ?>>
                                    <?php echo e($country); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Phone</label>
                        <input type="text" name="Phone" value="<?php echo e($booking['Phone']); ?>">
                    </div>
                </div>

                <div class="line"></div>

                <div class="reservationinfo">
                    <h4>Reservation information</h4>
                    <div class="field">
                        <label>Room type</label>
                        <select name="RoomType" class="selectinput" required>
                            <?php foreach (['Superior Room', 'Deluxe Room', 'Guest House', 'Single Room'] as $roomType): ?>
                                <option value="<?php echo e($roomType); ?>" <?php echo $booking['RoomType'] === $roomType ? 'selected' : ''; ?>>
                                    <?php echo e($roomType); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Bedding type</label>
                        <select name="Bed" class="selectinput" required>
                            <?php foreach (['Single', 'Double', 'Triple', 'Quad', 'None'] as $bedType): ?>
                                <option value="<?php echo e($bedType); ?>" <?php echo $booking['Bed'] === $bedType ? 'selected' : ''; ?>>
                                    <?php echo e($bedType); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Meal plan</label>
                        <select name="Meal" class="selectinput" required>
                            <?php foreach (['Room only', 'Breakfast', 'Half Board', 'Full Board'] as $mealPlan): ?>
                                <option value="<?php echo e($mealPlan); ?>" <?php echo $booking['Meal'] === $mealPlan ? 'selected' : ''; ?>>
                                    <?php echo e($mealPlan); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Number of rooms</label>
                        <select name="NoofRoom" class="selectinput">
                            <option value="1" <?php echo (int) $booking['NoofRoom'] === 1 ? 'selected' : ''; ?>>1</option>
                        </select>
                    </div>
                    <div class="datesection">
                        <div class="field">
                            <label>Check-in</label>
                            <input type="date" name="cin" value="<?php echo e($booking['cin']); ?>" required>
                        </div>
                        <div class="field">
                            <label>Check-out</label>
                            <input type="date" name="cout" value="<?php echo e($booking['cout']); ?>" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer">
                <button type="submit" name="guestdetailedit" class="btn btn-submit">Save changes</button>
            </div>
        </form>
    </div>
</body>

</html>