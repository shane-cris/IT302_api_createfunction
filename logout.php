<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/includes/helpers.php';

$_SESSION = [];
session_unset();
session_destroy();

redirect('index.php');