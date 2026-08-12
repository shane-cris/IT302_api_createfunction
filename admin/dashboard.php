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
    <title>Dashboard — BlueBird Admin</title>
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="admin-page">
    <?php $profitTotal = fetch_one('SELECT COALESCE(SUM(finaltotal), 0) AS Total FROM payment'); ?>
    <?php $totalRevenue = (float) ($profitTotal['Total'] ?? 0); ?>
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">A quick overview of bookings, staff and revenue</p>
    </div>

    <div class="databox">
        <div class="stat-card stat-red">
            <i class="fa-solid fa-bed"></i>
            <div>
                <span>Total Bookings</span>
                <h2><?php echo count(fetch_all('SELECT id FROM roombook')); ?></h2>
            </div>
        </div>
        <div class="stat-card stat-green">
            <i class="fa-solid fa-people-group"></i>
            <div>
                <span>Total Staff</span>
                <h2><?php echo count(fetch_all('SELECT id FROM staff')); ?></h2>
            </div>
        </div>
        <div class="stat-card stat-blue">
            <i class="fa-solid fa-indian-rupee-sign"></i>
            <div>
                <span>Projected Profit (10%)</span>
                <h2>&#8377; <?php echo number_format($totalRevenue * 0.10); ?></h2>
            </div>
        </div>
    </div>

    <?php
    $roomCounts = [
        'Superior Room' => 0,
        'Deluxe Room'   => 0,
        'Guest House'   => 0,
        'Single Room'   => 0,
    ];
    foreach ($roomCounts as $type => $value) {
        $roomCounts[$type] = (int) (fetch_one('SELECT COUNT(*) AS C FROM roombook WHERE RoomType = ?', 's', [$type])['C'] ?? 0);
    }

    $profitRows = fetch_all(
        "SELECT cout AS label, SUM(finaltotal) AS revenue FROM payment GROUP BY cout ORDER BY cout"
    );
    $profitLabels = [];
    $profitData = [];
    foreach ($profitRows as $row) {
        $profitLabels[] = $row['label'];
        $profitData[] = round((float) $row['revenue'] * 0.10, 2);
    }
    ?>

    <div class="chartbox">
        <div class="chart-card">
            <canvas id="bookroomchart" height="260"></canvas>
            <h3 class="chart-caption">Bookings by room type</h3>
        </div>
        <div class="chart-card">
            <canvas id="profitchart" height="260"></canvas>
            <h3 class="chart-caption">Profit by checkout date</h3>
        </div>
    </div>

    <script>
        const roomData = {
            labels: Object.keys(<?php echo json_encode($roomCounts); ?>),
            datasets: [{
                data: Object.values(<?php echo json_encode($roomCounts); ?>),
                backgroundColor: ['#e74c3c', '#f39c12', '#3498db', '#9b59b6'],
                borderWidth: 0,
            }]
        };

        new Chart(document.getElementById('bookroomchart'), {
            type: 'doughnut',
            data: roomData,
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        new Chart(document.getElementById('profitchart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($profitLabels); ?>,
                datasets: [{
                    label: 'Profit (₹)',
                    data: <?php echo json_encode($profitData); ?>,
                    backgroundColor: 'rgba(201, 162, 39, 0.85)',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>

</html>