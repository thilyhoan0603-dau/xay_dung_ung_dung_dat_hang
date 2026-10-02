<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Giỏ hàng - ShopeeFood Đà Nẵng</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        /* HEADER */

        header {
            background: #ff5722;
            color: white;
            padding: 18px 0;
        }

        .header-container {
            width: 90%;
            max-width: 1200px;
            margin: auto;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }


        /* CONTAINER */

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
        }

        h1 {
            margin-bottom: 25px;
        }


        /* THÔNG BÁO */

        .success {
            background: #d4edda;
            color: #155724;

            padding: 15px;
            border-radius: 8px;

            margin-bottom: 20px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;

            padding: 15px;
            border-radius: 8px;

            margin-bottom: 20px;
        }


        /* CART */

        .cart-box {
            background: white;
            border-radius: 12px;
            padding: 20px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .cart-item {
            display: flex;
            align-items: center;

            padding: 18px 0;

            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }


        /* IMAGE */

        .food-image {
            width: 90px;
            height: 90px;

            object-fit: cover;

            border-radius: 10px;

            margin-right: 20px;
        }


        /* INFO */

        .food-info {
            flex: 1;
        }

        .food-name {
            font-size: 18px;
            font-weight: bold;

            margin-bottom: 8px;
        }

        .food-price {
            color: #ff5722;
            font-weight: bold;
        }

        .quantity {
            margin-top: 8px;
            color: #666;
        }


        /* REMOVE */

        .remove-btn {
            background: #eee;
            color: #333;

            padding: 8px 12px;

            border-radius: 6px;

            text-decoration: none;
        }

        .remove-btn:hover {
            background: #ddd;
        }


        /* TOTAL */

        .total-box {
            margin-top: 25px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-text {
            font-size: 22px;
            font-weight: bold;
        }

        .total-price {
            color: #ff5722;
        }


        /* BUTTON */

        .checkout-btn {
            display: inline-block;

            background: #ff5722;
            color: white;

            padding: 14px 25px;

            border-radius: 8px;

            text-decoration: none;

            font-weight: bold;

            border: none;

            cursor: pointer;
        }

        .checkout-btn:hover {
            background: #e64a19;
        }


        .continue-btn {
            display: inline-block;

            margin-top: 20px;

            color: #ff5722;

            text-decoration: none;

            font-weight: bold;
        }


        /* EMPTY */

        .empty {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }


        /* MOBILE */

        @media (max-width: 600px) {

            .header-container {
                flex-direction: column;
                gap: 15px;
            }

            nav a {
                margin: 0 8px;
            }

            .cart-item {
                flex-wrap: wrap;
            }

            .food-image {
                width: 70px;
                height: 70px;
            }

            .total-box {
                flex-direction: column;
                align-items: stretch;
                gap: 20px;
            }

            .checkout-btn {
                text-align: center;
            }
        }

    </style>

</head>

<body>


<!-- HEADER -->

<header>

    <div class="header-container">

        <div class="logo">
            🛵 ShopeeFood Đà Nẵng
        </div>

        <nav>

            <a href="{{ url('/') }}">
                Trang chủ
            </a>

            <a href="{{ url('/orders') }}">
                Đơn hàng
            </a>

            <a href="{{ url('/cart') }}">
                🛒 Giỏ hàng
            </a>

            @if(session('user'))

                <a href="{{ url('/logout') }}">
                    Đăng xuất
                </a>

            @else

                <a href="{{ url('/login') }}">
                    Đăng nhập
                </a>

            @endif

        </nav>

    </div>

</header>



<!-- CONTENT -->

<div class="container">

    <h1>🛒 Giỏ hàng</h1>


    <!-- SUCCESS -->

    @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif


    <!-- ERROR -->

    @if(session('error'))

        <div class="error">
            {{ session('error') }}
        </div>

    @endif



    @if(count($cart) > 0)

        <div class="cart-box">


            <!-- DANH SÁCH MÓN -->

            @foreach($cart as $item)

                <div class="cart-item">


                    @if(!empty($item['image']))

                        <img
                            src="{{ asset('images/foods/' . $item['image']) }}"
                            class="food-image"
                            alt="{{ $item['name'] }}"
                        >

                    @else

                        <div
                            class="food-image"
                            style="
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                background:#eee;
                                font-size:35px;
                            "
                        >
                            🍽️
                        </div>

                    @endif



                    <div class="food-info">

                        <div class="food-name">
                            {{ $item['name'] }}
                        </div>

                        <div class="food-price">

                            {{ number_format($item['price'], 0, ',', '.') }} đ

                        </div>

                        <div class="quantity">

                            Số lượng:
                            <strong>
                                {{ $item['quantity'] }}
                            </strong>

                        </div>

                    </div>



                    <a
                        href="{{ url('/cart/remove/' . $item['id']) }}"
                        class="remove-btn"
                    >
                        Xóa
                    </a>

                </div>

            @endforeach



            <!-- TỔNG TIỀN -->

            <div class="total-box">

                <div class="total-text">

                    Tổng tiền:

                    <span class="total-price">

                        {{ number_format($total, 0, ',', '.') }} đ

                    </span>

                </div>


                <!-- QUAN TRỌNG -->

                <a
                    href="{{ url('/checkout') }}"
                    class="checkout-btn"
                >
                    🛵 Đặt hàng
                </a>

            </div>


        </div>


        <a
            href="{{ url('/') }}"
            class="continue-btn"
        >
            ← Tiếp tục mua hàng
        </a>


    @else


        <!-- GIỎ TRỐNG -->

        <div class="cart-box empty">

            <div class="empty-icon">
                🛒
            </div>

            <h2>
                Giỏ hàng đang trống
            </h2>

            <p>
                Hãy chọn món ăn yêu thích của bạn.
            </p>

            <a
                href="{{ url('/') }}"
                class="checkout-btn"
            >
                Xem nhà hàng
            </a>

        </div>

    @endif


</div>

</body>
</html>