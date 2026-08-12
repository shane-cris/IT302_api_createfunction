<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('roombook.php');
}

$id = (int) ($_POST['id'] ?? 0);
$booking = $id > 0 ? fetch_one('SELECT * FROM roombook WHERE id = ?', 'i', [$id]) : null;

if ($booking === null) {
    redirect('roombook.php');
}

if ($booking['stat'] === 'NotConfirm') {
    // Mark the booking as confirmed.
    run_mutation('UPDATE roombook SET stat = ? WHERE id = ?', 'si', ['Confirm', $id]);

    // Recalculate the bill using the shared pricing rules.
    $prices = booking_prices($booking['RoomType'], $booking['Bed'], $booking['Meal']);
    $noOfDays = (int) $booking['nodays'];
    $noOfRooms = (int) $booking['NoofRoom'];

    $roomTotal = $prices['room'] * $noOfDays * $noOfRooms;
    $bedTotal = $prices['bed'] * $noOfDays;
    $mealTotal = $prices['meal'] * $noOfDays;
    $finalTotal = $roomTotal + $bedTotal + $mealTotal;

    try {
        insert_row(
            "INSERT INTO payment
                (id, Name, Email, RoomType, Bed, NoofRoom, cin, cout, noofdays, roomtotal, bedtotal, meal, mealtotal, finaltotal)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'issssissiddsdd',
            [
                $id,
                $booking['Name'],
                $booking['Email'],
                $booking['RoomType'],
                $booking['Bed'],
                $noOfRooms,
                $booking['cin'],
                $booking['cout'],
                $noOfDays,
                $roomTotal,
                $bedTotal,
                $booking['Meal'],
                $mealTotal,
                $finalTotal,
            ]
        );
        flash_alert('Booking confirmed and payment recorded', 'success');
        redirect('roombook.php');
    } catch (Exception $e) {
        // A payment row already exists for this booking, so update it instead.
        run_mutation(
            "UPDATE payment SET
                Name = ?, Email = ?, RoomType = ?, Bed = ?, NoofRoom = ?,
                cin = ?, cout = ?, noofdays = ?, roomtotal = ?, bedtotal = ?, meal = ?, mealtotal = ?, finaltotal = ?
             WHERE id = ?",
            'ssssissiddsddi',
            [
                $booking['Name'],
                $booking['Email'],
                $booking['RoomType'],
                $booking['Bed'],
                $noOfRooms,
                $booking['cin'],
                $booking['cout'],
                $noOfDays,
                $roomTotal,
                $bedTotal,
                $booking['Meal'],
                $mealTotal,
                $finalTotal,
                $id,
            ]
        );
        flash_alert('Booking confirmed and payment updated', 'success');
        redirect('roombook.php');
    }
}

redirect('roombook.php');