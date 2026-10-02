<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng ký - ShopeeFood</title>

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
        <h1>Đăng ký tài khoản</h1>
    </section>


    <div class="search-box">

        <form action="#" method="POST">

            @csrf

            <div style="margin-bottom: 15px;">

                <label>Họ và tên</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Nhập họ và tên"
                    required
                    style="width: 100%; margin-top: 8px;"
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Nhập email"
                    required
                    style="width: 100%; margin-top: 8px;"
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>Số điện thoại</label>

                <input
                    type="text"
                    name="phone"
                    placeholder="Nhập số điện thoại"
                    style="width: 100%; margin-top: 8px;"
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>Địa chỉ</label>

                <input
                    type="text"
                    name="address"
                    placeholder="Nhập địa chỉ"
                    style="width: 100%; margin-top: 8px;"
                >

            </div>


            <div style="margin-bottom: 20px;">

                <label>Mật khẩu</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Nhập mật khẩu"
                    required
                    style="width: 100%; margin-top: 8px;"
                >

            </div>


            <button
                type="submit"
                style="width: 100%; height: 45px; border: none; border-radius: 6px; background: #ee4d2d; color: white; cursor: pointer;"
            >
                Đăng ký
            </button>

        </form>


        <p style="margin-top: 20px; text-align: center;">

            Đã có tài khoản?

            <a
                href="{{ url('/login') }}"
                style="color: #ee4d2d;"
            >
                Đăng nhập
            </a>

        </p>

    </div>

</main>


<footer>
    ShopeeFood Đà Nẵng © {{ date('Y') }}
</footer>

</body>

</html>