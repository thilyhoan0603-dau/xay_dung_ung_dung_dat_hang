<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        {{ $restaurant->name }} - ShopeeFood
    </title>

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
            text-decoration: none;
            color: #333;
        }

        .nav a:hover {
            color: #ee4d2d;
        }

        .restaurant-header {
            background: white;
            margin-top: 25px;
            padding: 25px;
            border-radius: 8px;
        }

        .restaurant-header h2 {
            color: #ee4d2d;
            margin-bottom: 12px;
        }

        .restaurant-header p {
            margin: 8px 0;
            color: #666;
        }

        .rating {
            color: #f59e0b !important;
            font-weight: bold;
        }

        .menu-title {
            margin: 30px 0 20px;
        }

        .food-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .food-card {
            display: flex;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        .food-image {
            width: 180px;
            height: 170px;
            flex-shrink: 0;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .food-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .food-no-image {
            font-size: 50px;
        }

        .food-info {
            padding: 18px;
            flex: 1;
        }

        .food-info h3 {
            margin-bottom: 10px;
        }

        .description {
            color: #777;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .food-price {
            color: #ee4d2d;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .btn-add {
            display: inline-block;
            padding: 10px 15px;
            background: #ee4d2d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-add:hover {
            background: #d84324;
        }

        .empty-food {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 8px;
        }

        footer {
            margin-top: 50px;
            padding: 25px;
            background: #333;
            color: white;
            text-align: center;
        }

        @media(max-width: 850px) {

            .food-list {
                grid-template-columns: 1fr;
            }

        }

        @media(max-width: 550px) {

            .food-card {
                display: block;
            }

            .food-image {
                width: 100%;
            }

            .nav .container {
                flex-wrap: wrap;
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

        <a href="/">
            Trang chủ
        </a>

        <a href="/orders">
            Đơn hàng
        </a>

        <a href="/cart">
            🛒 Giỏ hàng
        </a>

        <a href="/login">
            Đăng nhập
        </a>

        <a href="/register">
            Đăng ký
        </a>

    </div>

</nav>


<div class="container">


    <!-- THÔNG TIN NHÀ HÀNG -->

    <div class="restaurant-header">

        <h2>
            {{ $restaurant->name }}
        </h2>

        <p>
            📍 {{ $restaurant->address }}
        </p>

        <p class="rating">
            ⭐
            {{ $restaurant->rating ?? '5.0' }}
        </p>

        <p>
            🕐
            {{ $restaurant->delivery_time ?? '30-45 phút' }}
        </p>

    </div>


    <!-- DANH SÁCH MÓN -->

    <h2 class="menu-title">
        🍽️ Thực đơn
    </h2>


    @if($foods->count() > 0)

        <div class="food-list">

            @foreach($foods as $food)

                <div class="food-card">


                    <!-- HÌNH MÓN ĂN -->

                    <div class="food-image">

                        @if(!empty($food->image))

                            <img
                                src="{{ asset('images/foods/' . $food->image) }}"
                                alt="{{ $food->name }}"
                            >

                        @else

                            <span class="food-no-image">
                                🍽️
                            </span>

                        @endif

                    </div>


                    <!-- THÔNG TIN MÓN -->

                    <div class="food-info">

                        <h3>
                            {{ $food->name }}
                        </h3>

                        @if(!empty($food->description))

                            <p class="description">
                                {{ $food->description }}
                            </p>

                        @endif


                        <div class="food-price">

                            {{ number_format(
                                $food->price,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </div>


                        <!-- THÊM VÀO GIỎ -->

                        <a
                            href="{{ url('/cart/add/' . $food->id) }}"
                            class="btn-add"
                        >
                            + Thêm vào giỏ hàng
                        </a>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-food">

            <h3>
                Nhà hàng chưa có món ăn
            </h3>

            <p style="margin-top:10px;">
                Vui lòng quay lại sau.
            </p>

        </div>

    @endif


</div>


<footer>

    © 2026 ShopeeFood - Ứng dụng đặt đồ ăn

</footer>


</body>

</html>