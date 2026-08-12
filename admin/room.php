<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();

/**
 * Add a new room.
 */
if (isset($_POST['addroom'])) {
    $roomType = trim($_POST['troom'] ?? '');
    $bedding = trim($_POST['bed'] ?? '');

    if ($roomType !== '' && $bedding !== '') {
        insert_row("INSERT INTO room (type, bedding) VALUES (?, ?)", 'ss', [$roomType, $bedding]);
        redirect('room.php');
    }
}

$rooms = fetch_all('SELECT * FROM room ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms — BlueBird Admin</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="admin-page">
    <div class="addroomsection">
        <form action="" method="POST">
            <div class="field">
                <label for="troom">Type of room</label>
                <select name="troom" id="troom" class="form-control">
                    <option value="" selected disabled></option>
                    <option value="Superior Room">Superior Room</option>
                    <option value="Deluxe Room">Deluxe Room</option>
                    <option value="Guest House">Guest House</option>
                    <option value="Single Room">Single Room</option>
                </select>
            </div>
            <div class="field">
                <label for="bed">Type of bed</label>
                <select name="bed" id="bed" class="form-control">
                    <option value="" selected disabled></option>
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Triple">Triple</option>
                    <option value="Quad">Quad</option>
                    <option value="None">None</option>
                </select>
            </div>
            <button type="submit" class="btn-submit" name="addroom">Add room</button>
        </form>
    </div>

    <div class="room-grid">
        <?php if ($rooms === []): ?>
            <div class="empty-state">No rooms added yet. Add one above.</div>
        <?php endif; ?>

        <?php
        $tones = [
            'Superior Room' => 'tone-superior',
            'Deluxe Room'   => 'tone-deluxe',
            'Guest House'   => 'tone-guest',
            'Single Room'   => 'tone-single',
        ];
        foreach ($rooms as $row):
            $tone = $tones[$row['type']] ?? 'tone-superior';
        ?>
            <div class="room-card <?php echo $tone; ?>">
                <i class="fa-solid fa-bed icon-big"></i>
                <h3><?php echo e($row['type']); ?></h3>
                <p><?php echo e($row['bedding']); ?> bed</p>
                <form action="./roomdelete.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo e($row['id']); ?>">
                    <button class="btn-mini btn-mini-danger" onclick="return confirm('Delete this room?')">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>