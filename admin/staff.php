<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();

/**
 * Add a new staff member.
 */
if (isset($_POST['addstaff'])) {
    $staffName = trim($_POST['staffname'] ?? '');
    $staffWork = trim($_POST['staffwork'] ?? '');

    if ($staffName !== '' && $staffWork !== '') {
        insert_row("INSERT INTO staff (name, work) VALUES (?, ?)", 'ss', [$staffName, $staffWork]);
        redirect('staff.php');
    }
}

$staffList = fetch_all('SELECT * FROM staff ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff — BlueBird Admin</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="admin-page">
    <div class="addroomsection">
        <form action="" method="POST">
            <div class="field">
                <label for="staffname">Name</label>
                <input type="text" name="staffname" id="staffname" class="form-control" placeholder="Staff name" required>
            </div>
            <div class="field">
                <label for="staffwork">Work</label>
                <select name="staffwork" id="staffwork" class="form-control">
                    <option value="" selected disabled></option>
                    <option value="Manager">Manager</option>
                    <option value="Cook">Cook</option>
                    <option value="Helper">Helper</option>
                    <option value="Cleaner">Cleaner</option>
                    <option value="Waiter">Waiter</option>
                </select>
            </div>
            <button type="submit" class="btn-submit" name="addstaff">Add staff</button>
        </form>
    </div>

    <div class="room-grid">
        <?php if ($staffList === []): ?>
            <div class="empty-state">No staff members added yet.</div>
        <?php endif; ?>

        <?php foreach ($staffList as $row): ?>
            <div class="room-card tone-staff">
                <i class="fa-solid fa-user-tie icon-big"></i>
                <h3><?php echo e($row['name']); ?></h3>
                <p><?php echo e($row['work']); ?></p>
                <form action="./staffdelete.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo e($row['id']); ?>">
                    <button class="btn-mini btn-mini-danger" onclick="return confirm('Remove this staff member?')">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>