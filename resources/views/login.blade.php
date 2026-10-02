<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập - ShopeeFood</title>

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

        /* HEADER */
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
            font-size: 28px;
        }

        /* NAV */
        .nav {
            background: white;
            border-bottom: 1px solid #ddd;
        }

        .nav .container {
            display: flex;
            align-items: center;
            gap: 30px;
            padding: 15px 0;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
        }

        .nav a:hover {
            color: #ee4d2d;
        }

        /* LOGIN */
        .login-wrapper {
            min-height: 600px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 420px;
            background: white;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.12);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ee4d2d;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #ee4d2d;
        }

        /* BUTTON */
        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 6px;
            background: #ee4d2d;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #d84324;
        }

        /* ERROR */
        .error-message {
            background: #ffe5e5;
            color: #d60000;
            border: 1px solid #ffb3b3;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        /* SUCCESS */
        .success-message {
            background: #e7f8ed;
            color: #16803c;
            border: 1px solid #a9e5bd;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        /* REGISTER */
        .register-link {
            text-align: center;
            margin-top: 22px;
        }

        .register-link a {
            color: #ee4d2d;
            text-decoration: none;
            font-weight: bold;
        }

        /* FOOTER */
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
        }

        /* MOBILE */
        @media (max-width: 600px) {

            .nav .container {
                flex-wrap: wrap;
                gap: 15px;
            }

            .login-box {
                width: 100%;
                padding: 25px;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <h1>ShopeeFood</h1>
        </div>
    </header>


    <!-- NAVIGATION -->
    <nav class="nav">
        <div class="container">

            <a href="/">Trang chủ</a>

            <a href="/orders">Đơn hàng</a>

            <a href="/cart">Giỏ hàng</a>

            <a href="/login">Đăng nhập</a>

            <a href="/register">Đăng ký</a>

        </div>
    </nav>


    <!-- LOGIN -->
    <main class="login-wrapper">

        <div class="login-box">

            <h2>Đăng nhập</h2>


            <!-- THÔNG BÁO LỖI -->
            @if(session('error'))

                <div class="error-message">
                    {{ session('error') }}
                </div>

            @endif


            <!-- THÔNG BÁO THÀNH CÔNG -->
            @if(session('success'))

                <div class="success-message">
                    {{ session('success') }}
                </div>

            @endif


            <!-- LỖI VALIDATION -->
            @if($errors->any())

                <div class="error-message">

                    @foreach($errors->all() as $error)

                        <div>{{ $error }}</div>

                    @endforeach

                </div>

            @endif


            <!-- FORM -->
            <form method="POST" action="/login">

                @csrf

                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Nhập email của bạn"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Mật khẩu</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Nhập mật khẩu"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn-login"
                >
                    Đăng nhập
                </button>

            </form>


            <p class="register-link">

                Chưa có tài khoản?

                <a href="/register">
                    Đăng ký ngay
                </a>

            </p>

        </div>

    </main>


    <!-- FOOTER -->
    <footer>

        <div class="container">

            © 2026 ShopeeFood - Ứng dụng đặt đồ ăn

        </div>

    </footer>

</body>
</html>