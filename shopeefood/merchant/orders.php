<?php

require_once "config/database.php";
require_once "config/auth.php";

require_login();

$user_id =
    $_SESSION["user"]["id"];


/*
|--------------------------------------------------------------------------
| Lấy danh sách đơn hàng
|--------------------------------------------------------------------------
*/

$orders = [];

$stmt = $conn->prepare(
    "SELECT
        o.*,
        r.name AS restaurant_name
     FROM orders o
     LEFT JOIN restaurants r
        ON o.restaurant_id = r.id
     WHERE o.customer_id = ?
     ORDER BY o.id DESC"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {

    $orders[] = $row;
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Đơn hàng - ShopeeFood
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

            <a href="cart.php">
                Giỏ hàng
            </a>

            <a href="logout.php">
                Đăng xuất
            </a>

        </nav>

    </div>

</header>


<main class="container">

    <h1 class="title">
        📦 Đơn hàng của tôi
    </h1>


    <?php if (count($orders) === 0): ?>

        <div class="alert">

            Bạn chưa có đơn hàng nào.

            <br><br>

            <a
                href="index.php"
                class="btn"
            >
                Đặt món ngay
            </a>

        </div>

    <?php else: ?>


        <div class="table-box">

            <table>

                <thead>

                    <tr>

                        <th>
                            Mã đơn
                        </th>

                        <th>
                            Nhà hàng
                        </th>

                        <th>
                            Tổng tiền
                        </th>

                        <th>
                            Thanh toán
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Chi tiết
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($orders as $order): ?>

                    <tr>

                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $order["order_code"]
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $order["restaurant_name"]
                                ?? "Không xác định"
                            ) ?>

                        </td>


                        <td>

                            <span class="price">

                                <?= money(
                                    $order["total"]
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?php

                            if (
                                $order["payment_method"]
                                === "cod"
                            ) {

                                echo "COD";

                            } else {

                                echo "Chuyển khoản";
                            }

                            ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                status_text(
                                    $order["status"]
                                )
                            ) ?>

                        </td>


                        <td>

                            <a
                                href="order_detail.php?id=<?= $order["id"] ?>"
                                class="btn"
                            >

                                Xem

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>


    <?php endif; ?>

</main>


<footer class="footer">

    ShopeeFood Đà Nẵng © 2026

</footer>

</body>

</html>