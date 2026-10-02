<?php

require_once "config/database.php";
require_once "config/auth.php";

$keyword = $_GET['keyword'] ?? '';

$restaurants = [];

if ($keyword != '') {

    $keyword_sql = "%" . $conn->real_escape_string($keyword) . "%";

    $sql = "
        SELECT *
        FROM restaurants
        WHERE status = 1
        AND (
            name LIKE '$keyword_sql'
            OR address LIKE '$keyword_sql'
        )
        ORDER BY rating DESC
    ";

} else {

    $sql = "
        SELECT *
        FROM restaurants
        WHERE status = 1
        ORDER BY rating DESC
    ";
}

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }
}

$user = current_user();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>ShopeeFood Đà Nẵng</title>

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

            <?php if ($user): ?>

                <span>
                    Xin chào,
                    <?= htmlspecialchars($user['name']) ?>
                </span>

                <a href="logout.php">
                    Đăng xuất
                </a>

            <?php else: ?>

                <a href="login.php">
                    Đăng nhập
                </a>

                <a href="register.php">
                    Đăng ký
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>


<main class="container">

    <div class="search-box">

        <form method="GET"
              style="display:flex;width:100%;gap:10px;">

            <input
                type="text"
                name="keyword"
                placeholder="Tìm kiếm nhà hàng, món ăn..."
                value="<?= htmlspecialchars($keyword) ?>"
            >

            <button class="btn"
                    type="submit">
                Tìm kiếm
            </button>

        </form>

    </div>


    <h2 class="title">
        Nhà hàng tại Đà Nẵng
    </h2>


    <div class="grid">

        <?php if (count($restaurants) > 0): ?>

            <?php foreach ($restaurants as $restaurant): ?>

                <div class="card">

                    <div class="card-body">

                        <h3>
                            <?= htmlspecialchars($restaurant['name']) ?>
                        </h3>

                        <p>
                            📍
                            <?= htmlspecialchars($restaurant['address']) ?>
                        </p>

                        <p>
                            ⭐
                            <?= htmlspecialchars($restaurant['rating'] ?? '5.0') ?>
                        </p>

                        <p>
                            ⏱
                            <?= htmlspecialchars($restaurant['delivery_time'] ?? '30-45 phút') ?>
                        </p>

                        <br>

                        <a
                            class="btn"
                            href="restaurant.php?id=<?= $restaurant['id'] ?>"
                        >
                            Xem nhà hàng
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                Chưa có nhà hàng nào.
            </p>

        <?php endif; ?>

    </div>

</main>


<footer class="footer">

    <div class="container">

        ShopeeFood Đà Nẵng © 2026

    </div>

</footer>

</body>

</html>