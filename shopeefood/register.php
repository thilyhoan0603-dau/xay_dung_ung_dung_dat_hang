<?php

require_once "config/database.php";
require_once "config/auth.php";

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Kiểm tra thông tin
    if ($name === "" || $email === "" || $password === "") {

        $error = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Email không hợp lệ.";

    } elseif ($password !== $confirm_password) {

        $error = "Mật khẩu xác nhận không khớp.";

    } elseif (strlen($password) < 6) {

        $error = "Mật khẩu phải có ít nhất 6 ký tự.";

    } else {

        // Kiểm tra email đã tồn tại
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        if (!$check) {
            $error = "Lỗi truy vấn: " . $conn->error;
        } else {

            $check->bind_param("s", $email);
            $check->execute();

            $result = $check->get_result();

            if ($result->num_rows > 0) {

                $error = "Email này đã được đăng ký.";

            } else {

                // Mã hóa mật khẩu
                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $role = "customer";

                // Thêm tài khoản
                $stmt = $conn->prepare(
                    "INSERT INTO users
                    (name, email, password, phone, address, role)
                    VALUES (?, ?, ?, ?, ?, ?)"
                );

                if (!$stmt) {

                    $error = "Lỗi SQL: " . $conn->error;

                } else {

                    $stmt->bind_param(
                        "ssssss",
                        $name,
                        $email,
                        $hashed_password,
                        $phone,
                        $address,
                        $role
                    );

                    if ($stmt->execute()) {

                        $success = "Đăng ký thành công! Bạn có thể đăng nhập.";

                    } else {

                        $error = "Đăng ký thất bại: " . $stmt->error;
                    }

                    $stmt->close();
                }
            }

            $check->close();
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

    <title>Đăng ký - ShopeeFood</title>

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

            <a href="login.php">
                Đăng nhập
            </a>

        </nav>

    </div>

</header>


<main class="container">

    <div class="form-box">

        <h2>
            Tạo tài khoản
        </h2>


        <?php if ($error !== ""): ?>

            <div class="alert error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <?php if ($success !== ""): ?>

            <div class="alert success">

                <?= htmlspecialchars($success) ?>

                <br><br>

                <a class="btn"
                   href="login.php">

                    Đăng nhập ngay

                </a>

            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label>
                    Họ và tên
                </label>

                <input
                    type="text"
                    name="name"
                    required
                    value="<?= htmlspecialchars($_POST["name"] ?? "") ?>"
                >

            </div>


            <div class="form-group">

                <label>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    required
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                >

            </div>


            <div class="form-group">

                <label>
                    Số điện thoại
                </label>

                <input
                    type="text"
                    name="phone"
                    value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>"
                >

            </div>


            <div class="form-group">

                <label>
                    Địa chỉ
                </label>

                <textarea
                    name="address"
                    rows="3"
                ><?= htmlspecialchars($_POST["address"] ?? "") ?></textarea>

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


            <div class="form-group">

                <label>
                    Nhập lại mật khẩu
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    required
                >

            </div>


            <button
                class="btn"
                type="submit"
            >

                Đăng ký

            </button>

        </form>


        <p style="margin-top:20px;text-align:center;">

            Đã có tài khoản?

            <a
                href="login.php"
                style="color:#ee4d2d;"
            >

                Đăng nhập

            </a>

        </p>

    </div>

</main>


<footer class="footer">

    ShopeeFood Đà Nẵng © 2026

</footer>


</body>

</html>