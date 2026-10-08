<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>ShopeeFood - Đặt đồ ăn</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .header {
            background: #ee4d2d;
            padding: 18px 0;
        }

        .container {
            width: 1100px;
            max-width: 92%;
            margin: auto;
        }

        .header h1 {
            color: white;
        }

        .nav {
            background: white;
            border-bottom: 1px solid #ddd;
        }

        .nav .container {
            display: flex;
            gap: 30px;
            padding: 15px 0;
        }

        .nav a {
            color: #333;
            text-decoration: none;
        }

        .nav a:hover {
            color: #ee4d2d;
        }

        .search {
            background: white;
            padding: 25px;
            margin-top: 25px;
            border-radius: 8px;
        }

        .search form {
            display: flex;
            gap: 10px;
        }

        .search input {
            flex: 1;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .search button {
            padding: 13px 25px;
            border: none;
            border-radius: 6px;
            background: #ee4d2d;
            color: white;
            cursor: pointer;
        }

        .restaurant-title {
            margin: 30px 0 20px;
        }

        .restaurant-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .restaurant-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        .restaurant-image {
            height: 180px;
            background: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 60px;
        }

        .restaurant-info {
            padding: 18px;
        }

        .restaurant-info h3 {
            margin-bottom: 10px;
        }

        .restaurant-info p {
            margin: 7px 0;
            color: #666;
        }

        .rating {
            color: #f59e0b;
            font-weight: bold;
        }

        .btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            padding: 12px;
            background: #ee4d2d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn:hover {
            background: #d84324;
        }

        .success {
            margin-top: 20px;
            background: #e7f8ed;
            color: #16803c;
            padding: 12px;
            border-radius: 6px;
        }

        footer {
            margin-top: 50px;
            padding: 25px;
            background: #333;
            color: white;
            text-align: center;
        }

        @media(max-width: 800px) {

            .restaurant-list {
                grid-template-columns: 1fr;
            }

            .search form {
                flex-direction: column;
            }

        }

    </style>

</head>

<body>


<header class="header">

    <div class="container">

        <h1>🍴 ShopeeFood</h1>

    </div>

</header>


<nav class="nav">

    <div class="container">

        <a href="/">Trang chủ</a>

        <a href="/orders">Đơn hàng</a>

        <a href="/cart">🛒 Giỏ hàng</a>

        @if($user)

            <a href="/logout">
                Đăng xuất
            </a>

            <span>
                Xin chào, {{ $user['name'] }}
            </span>

        @else

            <a href="/login">
                Đăng nhập
            </a>

            <a href="/register">
                Đăng ký
            </a>

        @endif

    </div>

</nav>


<div class="container">


    @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif


    <div class="search">

        <form method="GET" action="/">

            <input
                type="text"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Tìm nhà hàng hoặc món ăn..."
            >

            <button type="submit">
                Tìm kiếm
            </button>

        </form>

    </div>


    <h2 class="restaurant-title">
        Nhà hàng nổi bật
    </h2>


    @if($restaurants->count() > 0)

        <div class="restaurant-list">

            @foreach($restaurants as $restaurant)

                <div class="restaurant-card">

                    <div class="restaurant-image">
                        🍽️
                    </div>

                    <div class="restaurant-info">

                        <h3>
                            {{ $restaurant->name }}
                        </h3>

                        <p>
                            📍 {{ $restaurant->address }}
                        </p>

                        <p class="rating">
                            ⭐ {{ $restaurant->rating ?? '5.0' }}
                        </p>

                        <p>
                            🕐
                            {{ $restaurant->delivery_time ?? '30-45 phút' }}
                        </p>

                        <a
                            class="btn"
                            href="{{ url('/restaurant/' . $restaurant->restaurant_id) }}"
                        >
                            Xem nhà hàng
                        </a>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div style="
            background:white;
            padding:40px;
            margin-top:20px;
            text-align:center;
        ">

            Không tìm thấy nhà hàng.

        </div>

    @endif

</div>


<footer>

    © 2026 ShopeeFood - Ứng dụng đặt đồ ăn

</footer>


</body>

</html>