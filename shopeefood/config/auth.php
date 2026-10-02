<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function require_login()
{
    if (!isset($_SESSION['user'])) {
        header("Location: /login.php");
        exit;
    }
}

function require_role($roles)
{
    require_login();

    if (!is_array($roles)) {
        $roles = [$roles];
    }

    if (!in_array($_SESSION['user']['role'], $roles)) {
        die("Bạn không có quyền truy cập trang này.");
    }
}

function money($number)
{
    return number_format($number, 0, ',', '.') . " đ";
}

function status_text($status)
{
    $statuses = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'ready' => 'Đã chuẩn bị',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy'
    ];

    return $statuses[$status] ?? $status;
}

?>