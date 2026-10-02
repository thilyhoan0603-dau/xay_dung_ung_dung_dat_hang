<?php

require_once "config/database.php";
require_once "config/auth.php";

require_login();

$user_id = $_SESSION["user"]["id"];


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

$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();

$cart = $cart_result->fetch_assoc();

$cart_id = $cart["id"] ?? 0;


/*
|--------------------------------------------------------------------------
| Cập nhật số lượng
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["update_item"])) {

    $item_id = intval($_POST["item_id"]);
    $quantity = intval($_POST["quantity"]);

    if ($quantity <= 0) {

        $delete = $conn->prepare(
            "DELETE FROM cart_items
             WHERE id = ?"
        );

        $delete->bind_param(
            "i",
            $item_id
        );

        $delete->execute();

    } else {

        $update = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ?
             AND cart_id = ?"
        );

        $update->bind_param(
            "iii",
            $quantity,
            $item_id,
            $cart_id
        );

        $update->execute();
    }

    header("Location: cart.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Xóa món
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["delete_item"])) {

    $item_id = intval($_POST["item_id"]);

    $delete = $conn->prepare(
        "DELETE FROM cart_items
         WHERE id = ?
         AND cart_id = ?"
    );

    $delete->bind_param(
        "ii",
        $item_id,
        $cart_id
    );

    $delete->execute();

    header("Location: cart.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Lấy danh sách món trong giỏ
|--------------------------------------------------------------------------
*/

$items = [];

$total = 0;

if ($cart_id > 0) {

    $stmt = $conn->prepare(
        "SELECT
            ci.id AS cart_item_id,
            ci.quantity,
            f.id AS food_id,
            f.name,
            f.price,
            f.restaurant_id,
            r.name AS restaurant_name
         FROM cart_items ci
         INNER JOIN foods f
            ON ci.food_id = f.id
         INNER JOIN restaurants r
            ON f.restaurant_id = r.id
         WHERE ci.cart_id = ?
         ORDER BY ci.id DESC"
    );

    $stmt->bind_param(
        "i",
        $cart_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $row["subtotal"] =
            $row["price"] * $row["quantity"];

        $total += $row["subtotal"];

        $items[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Giỏ hàng - ShopeeFood</title>

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

            <a href="logout.php">
                Đăng xuất
            </a>

        </nav>

    </div>

</header>


<main class="container">

    <h1 class="title">
        🛒 Giỏ hàng của bạn
    </h1>


    <?php if (count($items) === 0): ?>

        <div class="alert">

            Giỏ hàng đang trống.

            <br><br>

            <a class="btn"
               href="index.php">

                Tiếp tục mua hàng

            </a>

        </div>

    <?php else: ?>


        <?php

        $restaurant_id =
            $items[0]["restaurant_id"];

        $same_restaurant = true;

        foreach ($items as $item) {

            if (
                $item["restaurant_id"]
                != $restaurant_id
            ) {

                $same_restaurant = false;
                break;
            }
        }

        ?>


        <?php if (!$same_restaurant): ?>

            <div class="alert error">

                Giỏ hàng hiện có món từ nhiều
                nhà hàng.

                <br>

                Bạn nên đặt từng nhà hàng riêng.

            </div>

        <?php endif; ?>


        <div class="table-box">

            <table>

                <thead>

                    <tr>

                        <th>
                            Món ăn
                        </th>

                        <th>
                            Nhà hàng
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

                        <th>
                            Xóa
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($items as $item): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $item["name"]
                            ) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $item["restaurant_name"]
                            ) ?>

                        </td>

                        <td>

                            <?= money(
                                $item["price"]
                            ) ?>

                        </td>

                        <td>

                            <form
                                method="POST"
                                style="display:flex;gap:5px;"
                            >

                                <input
                                    type="hidden"
                                    name="item_id"
                                    value="<?= $item["cart_item_id"] ?>"
                                >

                                <input
                                    type="number"
                                    name="quantity"
                                    value="<?= $item["quantity"] ?>"
                                    min="1"
                                    style="width:70px;"
                                >

                                <button
                                    class="btn"
                                    name="update_item"
                                    type="submit"
                                >
                                    Cập nhật
                                </button>

                            </form>

                        </td>

                        <td class="price">

                            <?= money(
                                $item["subtotal"]
                            ) ?>

                        </td>

                        <td>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="item_id"
                                    value="<?= $item["cart_item_id"] ?>"
                                >

                                <button
                                    class="btn"
                                    name="delete_item"
                                    type="submit"
                                >

                                    Xóa

                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>


        <div class="cart-total">

            <h2>
                Tổng tiền:
                <span class="price">
                    <?= money($total) ?>
                </span>
            </h2>

            <br>

            <?php if ($same_restaurant): ?>

                <a
                    href="checkout.php"
                    class="btn"
                >
                    Tiến hành đặt hàng
                </a>

            <?php else: ?>

                <button
                    class="btn"
                    disabled
                >
                    Không thể thanh toán
                </button>

            <?php endif; ?>

        </div>


    <?php endif; ?>

</main>


<footer class="footer">

    ShopeeFood Đà Nẵng © 2026

</footer>

</body>

</html>