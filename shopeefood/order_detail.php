<?php

require_once "config/database.php";
require_once "config/auth.php";

require_login();

$user_id =
    $_SESSION["user"]["id"];

$order_id =
    intval($_GET["id"] ?? 0);


if ($order_id <= 0) {

    die("Đơn hàng không hợp lệ.");

}


/*
|--------------------------------------------------------------------------
| Lấy đơn hàng
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        o.*,
        r.name AS restaurant_name,
        r.address AS restaurant_address
     FROM orders o
     LEFT JOIN restaurants r
        ON o.restaurant_id = r.id
     WHERE o.id = ?
     AND o.customer_id = ?
     LIMIT 1"
);

$stmt->bind_param(
    "ii",
    $order_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    die("Không tìm thấy đơn hàng.");

}


$order =
    $result->fetch_assoc();


/*
|--------------------------------------------------------------------------
| Lấy chi tiết món
|--------------------------------------------------------------------------
*/

$items = [];

$item_stmt = $conn->prepare(
    "SELECT *
     FROM order_items
     WHERE order_id = ?
     ORDER BY id ASC"
);

$item_stmt->bind_param(
    "i",
    $order_id
);

$item_stmt->execute();

$item_result =
    $item_stmt->get_result();


while (
    $row =
    $item_result->fetch_assoc()
) {

    $items[] = $row;

}


/*
|--------------------------------------------------------------------------
| Lấy lịch sử trạng thái
|--------------------------------------------------------------------------
*/

$history = [];

$history_stmt = $conn->prepare(
    "SELECT *
     FROM order_status_history
     WHERE order_id = ?
     ORDER BY id ASC"
);

$history_stmt->bind_param(
    "i",
    $order_id
);

$history_stmt->execute();

$history_result =
    $history_stmt->get_result();


while (
    $row =
    $history_result->fetch_assoc()
) {

    $history[] = $row;

}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Chi tiết đơn hàng
    </title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <div class="logo">
            ShopeeFood
        </div>

        <nav class="nav">

            <a href="index.php">
                Trang chủ
            </a>

            <a href="orders.php">
                Đơn hàng
            </a>

            <a href="cart.php">
                Giỏ hàng
            </a>

        </nav>

    </div>

</header>


<main class="container">

    <h1 class="title">

        Chi tiết đơn hàng

        <small>
            #<?= htmlspecialchars(
                $order["order_code"]
            ) ?>
        </small>

    </h1>


    <!-- THÔNG TIN ĐƠN -->

    <div class="restaurant-header">

        <h2>

            <?= htmlspecialchars(
                $order["restaurant_name"]
                ?? "Nhà hàng"
            ) ?>

        </h2>

        <p>

            📍

            <?= htmlspecialchars(
                $order["restaurant_address"]
                ?? ""
            ) ?>

        </p>

        <p>

            📦 Trạng thái:

            <strong>

                <?= htmlspecialchars(
                    status_text(
                        $order["status"]
                    )
                ) ?>

            </strong>

        </p>

        <p>

            💳 Thanh toán:

            <?= $order["payment_method"] === "cod"
                ? "Thanh toán khi nhận hàng"
                : "Chuyển khoản" ?>

        </p>

        <p>

            📍 Địa chỉ giao hàng:

            <?= htmlspecialchars(
                $order["delivery_address"]
            ) ?>

        </p>

    </div>


    <!-- DANH SÁCH MÓN -->

    <h2 class="title">
        Món đã đặt
    </h2>


    <div class="table-box">

        <table>

            <thead>

                <tr>

                    <th>
                        Món ăn
                    </th>

                    <th>
                        Đơn giá
                    </th>

                    <th>
                        Số lượng
                    </th>

                    <th>
                        Thành tiền
                    </th>

                </tr>

            </thead>


            <tbody>

            <?php foreach ($items as $item): ?>

                <tr>

                    <td>

                        <?= htmlspecialchars(
                            $item["food_name"]
                        ) ?>

                    </td>

                    <td>

                        <?= money(
                            $item["price"]
                        ) ?>

                    </td>

                    <td>

                        <?= $item["quantity"] ?>

                    </td>

                    <td>

                        <span class="price">

                            <?= money(
                                $item["price"]
                                * $item["quantity"]
                            ) ?>

                        </span>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>


    <!-- TỔNG TIỀN -->

    <div class="cart-total">

        <p>

            Tiền món:

            <?= money(
                $order["subtotal"]
            ) ?>

        </p>


        <p>

            Phí giao hàng:

            <?= money(
                $order["delivery_fee"]
            ) ?>

        </p>


        <p>

            Giảm giá:

            <?= money(
                $order["discount"]
            ) ?>

        </p>


        <br>


        <h2>

            Tổng cộng:

            <span class="price">

                <?= money(
                    $order["total"]
                ) ?>

            </span>

        </h2>

    </div>


    <!-- LỊCH SỬ TRẠNG THÁI -->

    <h2 class="title">

        Theo dõi đơn hàng

    </h2>


    <div class="table-box">

        <?php if (count($history) > 0): ?>

            <?php foreach ($history as $h): ?>

                <div
                    style="
                        padding:15px;
                        border-bottom:1px solid #eee;
                    "
                >

                    <strong>

                        <?= htmlspecialchars(
                            status_text(
                                $h["status"]
                            )
                        ) ?>

                    </strong>

                    <p>

                        <?= htmlspecialchars(
                            $h["note"] ?? ""
                        ) ?>

                    </p>

                    <small>

                        <?= htmlspecialchars(
                            $h["created_at"] ?? ""
                        ) ?>

                    </small>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                Chưa có lịch sử trạng thái.
            </p>

        <?php endif; ?>

    </div>


    <br>


    <a
        href="orders.php"
        class="btn"
    >

        ← Quay lại đơn hàng

    </a>

</main>


<footer class="footer">

    ShopeeFood Đà Nẵng © 2026

</footer>

</body>

</html>