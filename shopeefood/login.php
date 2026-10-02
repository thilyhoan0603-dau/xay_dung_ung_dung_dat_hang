<?php

require_once "config/database.php";
require_once "config/auth.php";

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $error = "Vui lòng nhập email và mật khẩu.";

    } else {

        $stmt = $conn->prepare(
            "SELECT *
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user"] = [
                    "id" => $user["id"],
                    "name" => $user["name"],
                    "email" => $user["email"],
                    "phone" => $user["phone"],
                    "address" => $user["address"],
                    "role" => $user["role"]
                ];

                if ($user["role"] === "admin") {

                    header("Location: admin/index.php");

                } elseif ($user["role"] === "merchant") {

                    header("Location: merchant/index.php");

                } elseif ($user["role"] === "driver") {

                    header("Location: driver/index.php");

                } else {

                    header("Location: index.php");
                }

                exit;

            } else {

                $error = "Mật khẩu không chính xác.";
            }

        } else {

            $error = "Email chưa được đăng ký.";
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

    <title>Đăng nhập - ShopeeFood</title>

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

            <a href="register.php">
                Đăng ký
            </a>

        </nav>

    </div>

</header>


<main class="container">

    <div class="form-box">

        <h2>
            Đăng nhập
        </h2>


        <?php if ($error): ?>

            <div class="alert error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >

            </div>


            <div class="form-group">

                <label>
                    Mật khẩu
                </label>

                <input
                    type="password"
                    name="password"
                    required
                >

            </div>


            <button class="btn"
                    type="submit">

                Đăng nhập

            </button>

        </form>


        <p style="margin-top:20px;text-align:center;">

            Chưa có tài khoản?

            <a href="register.php"
               style="color:#ee4d2d;">

                Đăng ký ngay

            </a>

        </p>

    </div>

</main>

</body>

</html>