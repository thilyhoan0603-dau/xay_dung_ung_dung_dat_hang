<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Thanh toán - ShopeeFood</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .header {
            background: #ee4d2d;
            padding: 18px 0;
            color: white;
        }

        .container {
            width: 1000px;
            max-width: 92%;
            margin: auto;
        }

        .nav {
            background: white;
            padding: 15px 0;
        }

        .nav a {
            margin-right: 25px;
            text-decoration: none;
            color: #333;
        }

        .checkout {
            margin-top: 30px;
        }

        .box {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        h2 {
            margin-bottom: 20px;
        }

        h3 {
            margin-bottom: 15px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .customer-info p {
            margin: 10px 0;
        }

        .total {
            text-align: right;
            font-size: 22px;
            color: #ee4d2d;
            font-weight: bold;
            margin-top: 20px;
        }

        .btn-order {
            width: 100%;
            padding: 15px;
            border: none;
            background: #ee4d2d;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-order:hover {
            background: #d84324;
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

    </div>

</nav>


<div class="container checkout">

    <h2>💳 Xác nhận đặt hàng</h2>


    <div class="box customer-info">

        <h3>Thông tin giao hàng</h3>

        @if($user)

            <p>
                <strong>Họ tên:</strong>
                {{ $user['name'] }}
            </p>

            <p>
                <strong>Email:</strong>
                {{ $user['email'] }}
            </p>

        @endif

    </div>


    <div class="box">

        <h3>🍽️ Món đã chọn</h3>

        @foreach($cart as $item)

            <div class="item">

                <span>
                    {{ $item['name'] }}
                    × {{ $item['quantity'] }}
                </span>

                <strong>
                    {{ number_format(
                        $item['price'] * $item['quantity'],
                        0,
                        ',',
                        '.'
                    ) }}đ
                </strong>

            </div>

        @endforeach


        <div class="total">

            Tổng tiền:
            {{ number_format($total, 0, ',', '.') }}đ

        </div>

    </div>


    <div class="box">

        <form method="POST" action="/checkout">

            @csrf

            <button type="submit" class="btn-order">
                🛵 Xác nhận đặt hàng
            </button>

        </form>

    </div>

</div>

</body>

</html>