<?php

require_once "config/database.php";
require_once "config/auth.php";

require_login();

$user_id = $_SESSION["user"]["id"];

$error = "";


/*
|--------------------------------------------------------------------------
| Lấy thông tin người dùng
|--------------------------------------------------------------------------
*/

$user_stmt = $conn->prepare(
    "SELECT *
     FROM users
     WHERE id = ?
     LIMIT 1"
);

$user_stmt->bind_param(
    "i",
    $user_id
);

$user_stmt->execute();

$user = $user_stmt
    ->get_result()
    ->fetch_assoc();


/*
|--------------------------------------------------------------------------
| Lấy giỏ hàng
|--------------------------------------------------------------------------
*/

$cart_stmt = $conn->prepare(
    "SELECT id
     FROM carts
     WHERE user_id = ?
     LIMIT 1"
);

$cart_stmt->bind_param(
    "i",
    $user_id
);

$cart_stmt->execute();

$cart = $cart_stmt
    ->get_result()
    ->fetch_assoc();

$cart_id = $cart["id"] ?? 0;


if ($cart_id <= 0) {
    die("Giỏ hàng đang trống.");
}


/*
|--------------------------------------------------------------------------
| Lấy sản phẩm trong giỏ
|--------------------------------------------------------------------------
*/

$items = [];

$subtotal = 0;

$restaurant_id = 0;

$stmt = $conn->prepare(
    "SELECT
        ci.id AS cart_item_id,
        ci.quantity,
        f.id AS food_id,
        f.name,
        f.price,
        f.restaurant_id
     FROM cart_items ci
     INNER JOIN foods f
        ON ci.food_id = f.id
     WHERE ci.cart_id = ?"
);

$stmt->bind_param(
    "i",
    $cart_id
);

$stmt->execute();

$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {

    if ($restaurant_id === 0) {

        $restaurant_id =
            $row["restaurant_id"];

    }

    if (
        $restaurant_id
        != $row["restaurant_id"]
    ) {

        $error =
            "Giỏ hàng không được chứa món từ nhiều nhà hàng.";

    }

    $row["item_total"] =
        $row["price"] * $row["quantity"];

    $subtotal +=
        $row["item_total"];

    $items[] = $row;
}


if (count($items) === 0) {

    die("Giỏ hàng đang trống.");

}


/*
|--------------------------------------------------------------------------
| Phí giao hàng
|--------------------------------------------------------------------------
*/

$delivery_fee = 15000;

$discount = 0;

$total =
    $subtotal
    + $delivery_fee
    - $discount;


/*
|--------------------------------------------------------------------------
| Đặt hàng
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $delivery_address =
        trim($_POST["delivery_address"] ?? "");

    $payment_method =
        $_POST["payment_method"] ?? "cod";


    if ($delivery_address === "") {

        $error =
            "Vui lòng nhập địa chỉ giao hàng.";

    } elseif (
        $restaurant_id <= 0
    ) {

        $error =
            "Không xác định được nhà hàng.";

    } elseif (
        count($items) === 0
    ) {

        $error =
            "Giỏ hàng đang trống.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Tạo mã đơn hàng
        |--------------------------------------------------------------------------
        */

        $order_code =
            "SF"
            . date("YmdHis")
            . rand(100, 999);


        /*
        |--------------------------------------------------------------------------
        | Trạng thái ban đầu
        |--------------------------------------------------------------------------
        */

        $status = "pending";


        /*
        |--------------------------------------------------------------------------
        | Tạo đơn hàng
        |--------------------------------------------------------------------------
        */

        $insert = $conn->prepare(
            "INSERT INTO orders
            (
                order_code,
                customer_id,
                restaurant_id,
                subtotal,
                delivery_fee,
                discount,
                total,
                delivery_address,
                payment_method,
                status
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );


        $insert->bind_param(
            "siiddddsss",
            $order_code,
            $user_id,
            $restaurant_id,
            $subtotal,
            $delivery_fee,
            $discount,
            $total,
            $delivery_address,
            $payment_method,
            $status
        );


        if ($insert->execute()) {

            $order_id =
                $conn->insert_id;


            /*
            |--------------------------------------------------------------------------
            | Thêm chi tiết đơn hàng
            |--------------------------------------------------------------------------
            */

            foreach ($items as $item) {

                $food_name =
                    $item["name"];

                $price =
                    $item["price"];

                $quantity =
                    $item["quantity"];


                $detail = $conn->prepare(
                    "INSERT INTO order_items
                    (
                        order_id,
                        food_id,
                        food_name,
                        price,
                        quantity
                    )
                    VALUES (?, ?, ?, ?, ?)"
                );


                $detail->bind_param(
                    "iisdi",
                    $order_id,
                    $item["food_id"],
                    $food_name,
                    $price,
                    $quantity
                );


                $detail->execute();
            }


            /*
            |--------------------------------------------------------------------------
            | Lưu lịch sử trạng thái
            |--------------------------------------------------------------------------
            */

            $note =
                "Đơn hàng mới được tạo.";

            $history = $conn->prepare(
                "INSERT INTO order_status_history
                (
                    order_id,
                    status,
                    note
                )
                VALUES (?, ?, ?)"
            );


            $history->bind_param(
                "iss",
                $order_id,
                $status,
                $note
            );


            $history->execute();


            /*
            |--------------------------------------------------------------------------
            | Xóa giỏ hàng
            |--------------------------------------------------------------------------
            */

            $delete_items = $conn->prepare(
                "DELETE FROM cart_items
                 WHERE cart_id = ?"
            );

            $delete_items->bind_param(
                "i",
                $cart_id
            );

            $delete_items->execute();


            /*
            |--------------------------------------------------------------------------
            | Chuyển đến chi tiết đơn
            |--------------------------------------------------------------------------
            */

            header(
                "Location: order_detail.php?id="
                . $order_id
            );

            exit;

        } else {

            $error =
                "Không thể tạo đơn hàng: "
                . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Thanh toán - ShopeeFood
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

            <a href="orders.php">
                Đơn hàng
            </a>

        </nav>

    </div>

</header>


<main class="container">

    <h1 class="title">
        Thanh toán
    </h1>


    <?php if ($error): ?>

        <div class="alert error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <div class="form-box">

        <h2>
            Thông tin giao hàng
        </h2>


        <form method="POST">

            <div class="form-group">

                <label>
                    Người nhận
                </label>

                <input
                    type="text"
                    value="<?= htmlspecialchars(
                        $user["name"]
                    ) ?>"
                    disabled
                >

            </div>


            <div class="form-group">

                <label>
                    Số điện thoại
                </label>

                <input
                    type="text"
                    value="<?= htmlspecialchars(
                        $user["phone"] ?? ""
                    ) ?>"
                    disabled
                >

            </div>


            <div class="form-group">

                <label>
                    Địa chỉ giao hàng
                </label>

                <textarea
                    name="delivery_address"
                    rows="4"
                    required
                ><?= htmlspecialchars(
                    $_POST["delivery_address"]
                    ?? $user["address"]
                    ?? ""
                ) ?></textarea>

            </div>


            <div class="form-group">

                <label>
                    Phương thức thanh toán
                </label>

                <select name="payment_method">

                    <option value="cod">
                        Thanh toán khi nhận hàng
                    </option>

                    <option value="banking">
                        Chuyển khoản ngân hàng
                    </option>

                </select>

            </div>


            <hr>

            <br>


            <p>
                Tiền món:
                <strong>
                    <?= money($subtotal) ?>
                </strong>
            </p>

            <p>
                Phí giao hàng:
                <strong>
                    <?= money($delivery_fee) ?>
                </strong>
            </p>

            <p>
                Giảm giá:
                <strong>
                    <?= money($discount) ?>
                </strong>
            </p>

            <br>

            <h2>

                Tổng thanh toán:

                <span class="price">
                    <?= money($total) ?>
                </span>

            </h2>

            <br>


            <button
                type="submit"
                class="btn"
            >

                Đặt hàng

            </button>

        </form>

    </div>

</main>


<footer class="footer">

    ShopeeFood Đà Nẵng © 2026

</footer>

</body>

</html>