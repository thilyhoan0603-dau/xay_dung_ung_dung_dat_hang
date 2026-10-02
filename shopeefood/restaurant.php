<?php

require_once "config/database.php";
require_once "config/auth.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Nhà hàng không hợp lệ.");
}


/*
|--------------------------------------------------------------------------
| Lấy thông tin nhà hàng
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT *
     FROM restaurants
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$restaurant_result = $stmt->get_result();

if ($restaurant_result->num_rows === 0) {
    die("Không tìm thấy nhà hàng.");
}

$restaurant = $restaurant_result->fetch_assoc();


/*
|--------------------------------------------------------------------------
| Thêm món vào giỏ hàng
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["food_id"])) {

    require_login();

    $food_id = intval($_POST["food_id"]);
    $quantity = intval($_POST["quantity"] ?? 1);

    if ($quantity < 1) {
        $quantity = 1;
    }

    /*
    | Kiểm tra món ăn thuộc nhà hàng
    */

    $food_check = $conn->prepare(
        "SELECT id
         FROM foods
         WHERE id = ?
         AND restaurant_id = ?
         AND status = 1
         LIMIT 1"
    );

    $food_check->bind_param(
        "ii",
        $food_id,
        $id
    );

    $food_check->execute();

    $food_result = $food_check->get_result();

    if ($food_result->num_rows === 0) {

        die("Món ăn không hợp lệ.");

    }


    /*
    | Lấy hoặc tạo giỏ hàng
    */

    $user_id = $_SESSION["user"]["id"];

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

    $cart_result = $cart_stmt->get_result();


    if ($cart_result->num_rows > 0) {

        $cart = $cart_result->fetch_assoc();

        $cart_id = $cart["id"];

    } else {

        $create_cart = $conn->prepare(
            "INSERT INTO carts (user_id)
             VALUES (?)"
        );

        $create_cart->bind_param(
            "i",
            $user_id
        );

        $create_cart->execute();

        $cart_id = $conn->insert_id;
    }


    /*
    | Kiểm tra món đã có trong giỏ chưa
    */

    $item_check = $conn->prepare(
        "SELECT id, quantity
         FROM cart_items
         WHERE cart_id = ?
         AND food_id = ?
         LIMIT 1"
    );

    $item_check->bind_param(
        "ii",
        $cart_id,
        $food_id
    );

    $item_check->execute();

    $item_result = $item_check->get_result();


    if ($item_result->num_rows > 0) {

        $item = $item_result->fetch_assoc();

        $new_quantity =
            $item["quantity"] + $quantity;

        $update_item = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ?"
        );

        $update_item->bind_param(
            "ii",
            $new_quantity,
            $item["id"]
        );

        $update_item->execute();

    } else {

        $insert_item = $conn->prepare(
            "INSERT INTO cart_items
             (cart_id, food_id, quantity)
             VALUES (?, ?, ?)"
        );

        $insert_item->bind_param(
            "iii",
            $cart_id,
            $food_id,
            $quantity
        );

        $insert_item->execute();
    }


    header(
        "Location: restaurant.php?id=" . $id .
        "&added=1"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Lấy danh sách món ăn
|--------------------------------------------------------------------------
*/

$foods = [];

$food_stmt = $conn->prepare(
    "SELECT *
     FROM foods
     WHERE restaurant_id = ?
     AND status = 1
     ORDER BY id DESC"
);

$food_stmt->bind_param(
    "i",
    $id
);

$food_stmt->execute();

$food_result = $food_stmt->get_result();

while ($row = $food_result->fetch_assoc()) {
    $foods[] = $row;
}

$user = current_user();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($restaurant["name"]) ?>
        - ShopeeFood
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
                🛒 Giỏ hàng
            </a>

            <a href="orders.php">
                Đơn hàng
            </a>

            <?php if ($user): ?>

                <a href="logout.php">
                    Đăng xuất
                </a>

            <?php else: ?>

                <a href="login.php">
                    Đăng nhập
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>


<main class="container">


    <?php if (isset($_GET["added"])): ?>

        <div class="alert success">

            Đã thêm món vào giỏ hàng!

            <a href="cart.php">
                Xem giỏ hàng
            </a>

        </div>

    <?php endif; ?>


    <div class="restaurant-header">

        <h1>
            <?= htmlspecialchars($restaurant["name"]) ?>
        </h1>

        <p>
            📍
            <?= htmlspecialchars($restaurant["address"]) ?>
        </p>

        <p>
            ⭐
            <?= htmlspecialchars($restaurant["rating"] ?? "5.0") ?>
        </p>

        <p>
            ⏱
            <?= htmlspecialchars(
                $restaurant["delivery_time"]
                ?? "30-45 phút"
            ) ?>
        </p>

    </div>


    <h2 class="title">
        Menu món ăn
    </h2>


    <div class="grid">

        <?php if (count($foods) > 0): ?>

            <?php foreach ($foods as $food): ?>

                <div class="card">

                    <div class="card-body">

                        <h3>
                            <?= htmlspecialchars($food["name"]) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $food["description"] ?? ""
                            ) ?>
                        </p>

                        <p class="price">

                            <?= money($food["price"]) ?>

                        </p>


                        <?php if ($user): ?>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="food_id"
                                    value="<?= $food["id"] ?>"
                                >

                                <div class="form-group">

                                    <label>
                                        Số lượng
                                    </label>

                                    <input
                                        type="number"
                                        name="quantity"
                                        value="1"
                                        min="1"
                                    >

                                </div>

                                <button
                                    class="btn"
                                    type="submit"
                                >

                                    Thêm vào giỏ

                                </button>

                            </form>

                        <?php else: ?>

                            <a
                                href="login.php"
                                class="btn"
                            >
                                Đăng nhập để đặt món
                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                Nhà hàng hiện chưa có món ăn.
            </p>

        <?php endif; ?>

    </div>

</main>


<footer class="footer">

    ShopeeFood Đà Nẵng © 2026

</footer>

</body>

</html>