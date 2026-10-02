<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đơn hàng - ShopeeFood</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

<header>
    <div class="header-container">

        <a href="{{ url('/') }}" class="logo">
            ShopeeFood
        </a>

        <nav>
            <a href="{{ url('/') }}">Trang chủ</a>
            <a href="{{ url('/orders') }}">Đơn hàng</a>
            <a href="{{ url('/cart') }}">Giỏ hàng</a>
            <a href="{{ url('/login') }}">Đăng nhập</a>
            <a href="{{ url('/register') }}">Đăng ký</a>
        </nav>

    </div>
</header>

<main>

    <section class="page-title">
        <h1>Đơn hàng của tôi</h1>
    </section>

    <div class="search-box">
        <p>Chưa có đơn hàng nào.</p>
    </div>

</main>

<footer>
    ShopeeFood Đà Nẵng © {{ date('Y') }}
</footer>

</body>
</html>