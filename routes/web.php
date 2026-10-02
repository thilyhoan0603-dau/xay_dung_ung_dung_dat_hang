<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    $restaurant = DB::table('restaurants')
        ->where('id', $id)
        ->where('status', 1)
        ->first();

    if (!$restaurant) {
        abort(404);
    }

    $foods = DB::table('foods')
        ->where('restaurant_id', $id)
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

    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    return view('cart', [
        'cart' => $cart,
        'total' => $total
    ]);
});


/*
|--------------------------------------------------------------------------
| THÊM MÓN VÀO GIỎ
|--------------------------------------------------------------------------
*/
Route::get('/cart/add/{foodId}', function ($foodId) {

    $food = DB::table('foods')
        ->where('id', $foodId)
        ->first();

    if (!$food) {
        abort(404);
    }

    $cart = session('cart', []);

    if (isset($cart[$foodId])) {

        $cart[$foodId]['quantity']++;

    } else {

        $cart[$foodId] = [
            'id' => $food->id,
            'name' => $food->name,
            'price' => $food->price,
            'quantity' => 1,
            'image' => $food->image ?? null,
        ];
    }

    session(['cart' => $cart]);

    return redirect('/cart')
        ->with('success', 'Đã thêm món vào giỏ hàng!');
});


/*
|--------------------------------------------------------------------------
| XÓA MÓN KHỎI GIỎ
|--------------------------------------------------------------------------
*/
Route::get('/cart/remove/{foodId}', function ($foodId) {

    $cart = session('cart', []);

    if (isset($cart[$foodId])) {
        unset($cart[$foodId]);
    }

    session(['cart' => $cart]);

    return redirect('/cart');
});


/*
|--------------------------------------------------------------------------
| TRANG CHECKOUT
|--------------------------------------------------------------------------
*/
Route::get('/checkout', function () {

    $cart = session('cart', []);

    if (empty($cart)) {
        return redirect('/cart')
            ->with('error', 'Giỏ hàng đang trống!');
    }

    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    return view('checkout', [
        'cart' => $cart,
        'total' => $total,
        'user' => session('user')
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
            ->with('error', 'Giỏ hàng đang trống!');
    }

    // Tạm thời xác nhận đặt hàng bằng session.
    // Chưa ghi database để tránh sai tên cột orders/order_items.

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

    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = DB::table('users')
        ->where('email', $request->email)
        ->first();

    if (!$user) {
        return back()
            ->withInput()
            ->with('error', 'Email không tồn tại!');
    }

    if (!Hash::check($request->password, $user->password)) {
        return back()
            ->withInput()
            ->with('error', 'Mật khẩu không đúng!');
    }

    session([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'customer',
        ]
    ]);

    return redirect('/')
        ->with('success', 'Đăng nhập thành công!');
});


/*
|--------------------------------------------------------------------------
| ĐĂNG XUẤT
|--------------------------------------------------------------------------
*/
Route::get('/logout', function () {

    session()->forget('user');

    return redirect('/')
        ->with('success', 'Đã đăng xuất!');
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
        'password' => 'required|min:6',
    ]);

    $exists = DB::table('users')
        ->where('email', $request->email)
        ->exists();

    if ($exists) {
        return back()
            ->withInput()
            ->with('error', 'Email đã tồn tại!');
    }

    DB::table('users')->insert([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
        'password' => Hash::make($request->password),
        'role' => 'customer',
    ]);

    return redirect('/login')
        ->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
});


/*
|--------------------------------------------------------------------------
| ĐƠN HÀNG
|--------------------------------------------------------------------------
*/
Route::get('/orders', function () {
    return view('orders');
});
=======

Route::get('/', function () {
    return view('welcome');
});
>>>>>>> 267fda9f420b6d201665f248d3888a649f1b340f
