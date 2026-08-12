<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();

$id = (int) ($_GET['id'] ?? 0);
$payment = $id > 0 ? fetch_one('SELECT * FROM payment WHERE id = ?', 'i', [$id]) : null;

if ($payment === null) {
    redirect('payment.php');
}

$prices = booking_prices($payment['RoomType'], $payment['Bed'], $payment['meal']);
$name = $payment['Name'];
$cin = $payment['cin'];
$cout = $payment['cout'];
$days = (int) $payment['noofdays'];
$rooms = (int) $payment['NoofRoom'];
$roomRate = $prices['room'];
$bedRate = $prices['bed'];
$mealRate = $prices['meal'];
$finalTotal = (float) $payment['finaltotal'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice #<?php echo e($payment['id']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background: #eef1f5;
            padding: 40px 20px;
        }

        .invoice {
            width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 24px 60px -18px rgba(14, 27, 61, 0.4);
            overflow: hidden;
        }

        .invoice-head {
            background: #0e1b3d;
            color: #fff;
            padding: 28px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .invoice-head h1 {
            font-size: 26px;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .invoice-head img {
            height: 54px;
            background: #fff;
            border-radius: 10px;
            padding: 4px;
        }

        .invoice-body {
            padding: 36px 40px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 28px;
        }

        .meta .block p {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .meta .block strong {
            color: #0e1b3d;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        th,
        td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #eef1f5;
            font-size: 14px;
        }

        th {
            background: #f7f4ee;
            color: #0e1b3d;
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td.num,
        th.num {
            text-align: right;
        }

        .balance {
            width: 320px;
            margin-left: auto;
            margin-bottom: 0;
        }

        .balance td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .balance .grand {
            background: #f7f4ee;
            font-size: 16px;
            color: #0e1b3d;
        }

        .invoice-foot {
            background: #f7f4ee;
            padding: 20px 40px 26px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }

        .invoice-foot strong {
            color: #0e1b3d;
        }

        .print-btn {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-btn button {
            background: #0e1b3d;
            color: #fff;
            border: none;
            padding: 12px 34px;
            border-radius: 40px;
            font-size: 15px;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }

        .print-btn button:hover {
            opacity: 0.85;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .print-btn {
                display: none;
            }

            .invoice {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-btn">
        <button onclick="window.print()">Print invoice</button>
    </div>

    <div class="invoice">
        <div class="invoice-head">
            <h1>Invoice</h1>
            <img src="../image/logo.jpg" alt="Hotel Blue Bird">
        </div>

        <div class="invoice-body">
            <div class="meta">
                <div class="block">
                    <p>Billed to</p>
                    <strong><?php echo e($name); ?></strong>
                </div>
                <div class="block">
                    <p>Invoice #</p>
                    <strong><?php echo e($payment['id']); ?></strong>
                </div>
                <div class="block">
                    <p>Check-out date</p>
                    <strong><?php echo e($cout); ?></strong>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Nights</th>
                        <th class="num">Rate</th>
                        <th class="num">Qty</th>
                        <th class="num">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo e($payment['RoomType']); ?></td>
                        <td><?php echo e($days); ?></td>
                        <td class="num">&#8377; <?php echo number_format($roomRate, 2); ?></td>
                        <td class="num"><?php echo e($rooms); ?></td>
                        <td class="num">&#8377; <?php echo number_format((float) $payment['roomtotal'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo e($payment['Bed']); ?> bed</td>
                        <td><?php echo e($days); ?></td>
                        <td class="num">&#8377; <?php echo number_format($bedRate, 2); ?></td>
                        <td class="num"><?php echo e($rooms); ?></td>
                        <td class="num">&#8377; <?php echo number_format((float) $payment['bedtotal'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo e($payment['meal']); ?></td>
                        <td><?php echo e($days); ?></td>
                        <td class="num">&#8377; <?php echo number_format($mealRate, 2); ?></td>
                        <td class="num"><?php echo e($rooms); ?></td>
                        <td class="num">&#8377; <?php echo number_format((float) $payment['mealtotal'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <table class="balance">
                <tr>
                    <td>Total</td>
                    <td>&#8377; <?php echo number_format($finalTotal, 2); ?></td>
                </tr>
                <tr>
                    <td>Amount paid</td>
                    <td>&#8377; 0.00</td>
                </tr>
                <tr class="grand">
                    <td>Balance due</td>
                    <td>&#8377; <?php echo number_format($finalTotal, 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="invoice-foot">
            <strong>Hotel Blue Bird</strong> · hello@bluebird.com · +91 93133 46569
        </div>
    </div>
</body>

</html>