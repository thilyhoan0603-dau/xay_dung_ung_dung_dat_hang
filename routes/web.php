<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;


/*
|--------------------------------------------------------------------------
| TRANG CHỦ
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index']);


/*
|--------------------------------------------------------------------------
| NHÀ HÀNG
|--------------------------------------------------------------------------
*/

Route::get('/restaurant/{id}', function ($id) {

    $restaurant = DB::table('nha_hang')
        ->where('ma_nh', $id)
        ->first();

    if (!$restaurant) {
        abort(404, 'Không tìm thấy nhà hàng');
    }

    $foods = DB::table('mon_an')
        ->where('ma_nh', $id)
        ->get();

    return view('restaurant', [
        'restaurant' => $restaurant,
        'foods' => $foods
    ]);
});


/*
|--------------------------------------------------------------------------
| GIỎ HÀNG
|--------------------------------------------------------------------------
*/

Route::get('/cart', function () {

    $cart = session('cart', []);

    return view('cart', [
        'cart' => $cart
    ]);
});


/*
|--------------------------------------------------------------------------
| THÊM MÓN VÀO GIỎ
|--------------------------------------------------------------------------
*/

Route::get('/cart/add/{id}', function ($id) {

    $food = DB::table('mon_an')
        ->where('ma_mon', $id)
        ->first();

    if (!$food) {
        return back()->with('error', 'Không tìm thấy món ăn');
    }

    $cart = session('cart', []);

    if (isset($cart[$id])) {

        $cart[$id]['quantity']++;

    } else {

        $cart[$id] = [
            'id' => $food->ma_mon,
            'name' => $food->ten_mon,
            'price' => $food->gia,
            'quantity' => 1
        ];
    }

    session([
        'cart' => $cart
    ]);

    return redirect('/cart')
        ->with('success', 'Đã thêm món vào giỏ hàng');
});


/*
|--------------------------------------------------------------------------
| XÓA MÓN KHỎI GIỎ
|--------------------------------------------------------------------------
*/

Route::get('/cart/remove/{id}', function ($id) {

    $cart = session('cart', []);

    if (isset($cart[$id])) {
        unset($cart[$id]);
    }

    session([
        'cart' => $cart
    ]);

    return redirect('/cart');
});


/*
|--------------------------------------------------------------------------
| XÓA TOÀN BỘ GIỎ HÀNG
|--------------------------------------------------------------------------
*/

Route::get('/cart/clear', function () {

    session()->forget('cart');

    return redirect('/cart')
        ->with('success', 'Đã xóa giỏ hàng');
});


/*
|--------------------------------------------------------------------------
| THANH TOÁN
|--------------------------------------------------------------------------
*/

Route::get('/checkout', function () {

    $cart = session('cart', []);

    if (empty($cart)) {
        return redirect('/cart')
            ->with('error', 'Giỏ hàng đang trống');
    }

    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    return view('checkout', [
        'cart' => $cart,
        'total' => $total
    ]);
});


/*
|--------------------------------------------------------------------------
| ĐẶT HÀNG
|--------------------------------------------------------------------------
*/

Route::post('/checkout', function (Request $request) {

    $cart = session('cart', []);

    if (empty($cart)) {
        return redirect('/cart')
            ->with('error', 'Giỏ hàng đang trống');
    }

    /*
     * Hiện tại lưu thông tin đơn vào session.
     * Sau khi xác định chính xác cấu trúc bảng don_hang,
     * có thể chuyển sang INSERT vào MySQL.
     */

    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    session([
        'last_order' => [
            'cart' => $cart,
            'total' => $total,
            'address' => $request->address,
            'payment' => $request->payment
        ]
    ]);

    session()->forget('cart');

    return redirect('/')
        ->with('success', 'Đặt hàng thành công!');
});


/*
|--------------------------------------------------------------------------
| ĐĂNG NHẬP
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('login');
});


Route::post('/login', function (Request $request) {

    $email = $request->email;
    $password = $request->password;

    $user = DB::table('khach_hang')
        ->where('email', $email)
        ->first();

    if (!$user) {
        return back()
            ->with('error', 'Email không tồn tại');
    }

    /*
     * Phần kiểm tra mật khẩu sẽ điều chỉnh theo
     * cấu trúc cột thực tế của bảng khach_hang.
     */

    session([
        'user' => $user
    ]);

    return redirect('/')
        ->with('success', 'Đăng nhập thành công');
});


/*
|--------------------------------------------------------------------------
| ĐĂNG XUẤT
|--------------------------------------------------------------------------
*/

Route::get('/logout', function () {

    session()->forget('user');

    return redirect('/')
        ->with('success', 'Đã đăng xuất');
});


/*
|--------------------------------------------------------------------------
| ĐĂNG KÝ
|--------------------------------------------------------------------------
*/

Route::get('/register', function () {
    return view('register');
});


Route::post('/register', function (Request $request) {

    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6'
    ]);

    return redirect('/login')
        ->with('success', 'Đăng ký thành công');
});


/*
|--------------------------------------------------------------------------
| ĐƠN HÀNG
|--------------------------------------------------------------------------
*/

Route::get('/orders', function () {

    $user = session('user');

    if (!$user) {
        return redirect('/login')
            ->with('error', 'Vui lòng đăng nhập');
    }

    return view('orders', [
        'orders' => []
    ]);
});