<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/countries.php';

/**
 * Escape a value for safe output inside HTML.
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to another location and stop the script.
 */
function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

/**
 * True when a user or staff member is logged in.
 */
function is_logged_in(): bool
{
    return isset($_SESSION['usermail']) && $_SESSION['usermail'] !== '';
}

/**
 * True when the current session belongs to a staff member.
 */
function is_staff(): bool
{
    return is_logged_in() && ($_SESSION['role'] ?? 'user') === 'staff';
}

/**
 * Relative path (from the current script) to the login page.
 * Works whether the script lives at the app root or inside /admin.
 */
function app_login_path(): string
{
    $fsRoot = realpath(__DIR__ . '/..');
    $fsScript = realpath($_SERVER['SCRIPT_FILENAME'] ?? '');
    if ($fsRoot === false || $fsScript === false || strpos($fsScript, $fsRoot) !== 0) {
        return 'index.php';
    }
    $rel = trim(str_replace('\\', '/', substr($fsScript, strlen($fsRoot))), '/');
    $depth = ($rel === '') ? 0 : substr_count($rel, '/');
    return str_repeat('../', $depth) . 'index.php';
}

/**
 * Relative path (from the current script) to the admin panel.
 */
function app_admin_path(): string
{
    return (app_login_path() === 'index.php') ? 'admin/admin.php' : '../admin/admin.php';
}

/**
 * Block access unless the visitor is a logged-in customer.
 */
function require_user(): void
{
    if (!is_logged_in()) {
        redirect(app_login_path());
    }
    if (is_staff()) {
        redirect(app_admin_path());
    }
}

/**
 * Block access unless the visitor is a logged-in staff member.
 */
function require_staff(): void
{
    if (!is_logged_in()) {
        redirect(app_login_path());
    }
    if (!is_staff()) {
        redirect(app_login_path());
    }
}

/**
 * Prepare and execute a statement (prepared statements prevent SQL injection).
 *
 * @param string $sql    Query with `?` placeholders
 * @param string $types  bind_param type string, e.g. 'ssi'
 * @param array  $params Values to bind
 */
function run_query(string $sql, string $types = '', array $params = []): mysqli_stmt
{
    $stmt = db()->prepare($sql);
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare query. ' . db()->error);
    }
    if ($params !== []) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new RuntimeException('Query execution failed. ' . $stmt->error);
    }
    return $stmt;
}

/**
 * Fetch every matching row as an associative array.
 */
function fetch_all(string $sql, string $types = '', array $params = []): array
{
    $stmt = run_query($sql, $types, $params);
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows !== false ? $rows : [];
}

/**
 * Fetch a single row (or null when nothing matches).
 */
function fetch_one(string $sql, string $types = '', array $params = []): ?array
{
    $stmt = run_query($sql, $types, $params);
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row !== null && $row !== false ? $row : null;
}

/**
 * Insert a row and return the new id.
 */
function insert_row(string $sql, string $types, array $params): int
{
    $stmt = run_query($sql, $types, $params);
    $id = (int) db()->insert_id;
    $stmt->close();
    return $id;
}

/**
 * Run an UPDATE/DELETE statement and return the affected rows.
 */
function run_mutation(string $sql, string $types = '', array $params = []): int
{
    $affected = run_query($sql, $types, $params)->affected_rows;
    return (int) $affected;
}

/**
 * Verify a password against a stored value.
 * Supports bcrypt hashes produced by password_hash() as well as
 * legacy plain-text values kept from the original database.
 */
function verify_password(string $password, ?string $stored): bool
{
    if ($stored === null || $stored === '') {
        return false;
    }
    $info = password_get_info($stored);
    if ($info['algo'] !== null && $info['algo'] !== 0) {
        return password_verify($password, $stored);
    }
    return hash_equals($stored, $password);
}

/**
 * Room pricing rules used when confirming or editing a booking.
 *
 * @return array{room: float, bed: float, meal: float}
 */
function booking_prices(string $roomType, string $bed, string $meal): array
{
    $roomRates = [
        'Superior Room' => 3000,
        'Deluxe Room'   => 2000,
        'Guest House'   => 1500,
        'Single Room'   => 1000,
    ];
    $bedPercent = [
        'Single' => 0.01,
        'Double' => 0.02,
        'Triple' => 0.03,
        'Quad'   => 0.04,
        'None'   => 0.0,
    ];
    $mealMultiplier = [
        'Room only' => 0,
        'Breakfast' => 2,
        'Half Board' => 3,
        'Full Board' => 4,
    ];

    $roomRate = $roomRates[$roomType] ?? 0;
    $bedRate = $roomRate * ($bedPercent[$bed] ?? 0);
    $mealRate = $bedRate * ($mealMultiplier[$meal] ?? 0);

    return ['room' => $roomRate, 'bed' => $bedRate, 'meal' => $mealRate];
}

/**
 * Flash a SweetAlert message on the next page load.
 */
function flash_alert(string $title, string $icon = 'info'): void
{
    $_SESSION['flash'][] = ['title' => $title, 'icon' => $icon];
}

/**
 * Output any queued SweetAlert messages and clear the queue.
 */
function render_flash_alerts(): void
{
    $alerts = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    foreach ($alerts as $alert) {
        $title = e($alert['title']);
        $icon = e($alert['icon']);
        echo "<script>swal({ title: '{$title}', icon: '{$icon}' });</script>";
    }
}