<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->input('keyword', ''));

        // Lấy danh sách cột bảng nha_hang
        $columns = Schema::getColumnListing('nha_hang');

        $query = DB::table('nha_hang');

        // Mã nhà hàng
        $idColumn = $this->findColumn($columns, [
            'ma_nh',
            'id',
            'ma_nha_hang'
        ]);

        // Tên nhà hàng
        $nameColumn = $this->findColumn($columns, [
            'ten_nha_hang',
            'ten_nh',
            'ten',
            'name'
        ]);

        // Địa chỉ
        $addressColumn = $this->findColumn($columns, [
            'dia_chi',
            'diachi',
            'address'
        ]);

        // Đánh giá
        $ratingColumn = $this->findColumn($columns, [
            'rating',
            'danh_gia',
            'so_sao'
        ]);

        // Hình ảnh
        $imageColumn = $this->findColumn($columns, [
            'hinh_anh',
            'anh',
            'hinh',
            'image'
        ]);

        // Thời gian giao hàng
        $deliveryColumn = $this->findColumn($columns, [
            'delivery_time',
            'thoi_gian_giao',
            'thoi_gian_giao_hang'
        ]);

        /*
        |--------------------------------------------------------------------------
        | TÌM KIẾM
        |--------------------------------------------------------------------------
        */

        if ($keyword !== '') {
            $query->where(function ($q) use (
                $keyword,
                $nameColumn,
                $addressColumn
            ) {
                if ($nameColumn) {
                    $q->where(
                        $nameColumn,
                        'like',
                        '%' . $keyword . '%'
                    );
                }

                if ($addressColumn) {
                    $q->orWhere(
                        $addressColumn,
                        'like',
                        '%' . $keyword . '%'
                    );
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | SẮP XẾP
        |--------------------------------------------------------------------------
        */

        if ($ratingColumn) {
            $query->orderByDesc($ratingColumn);
        }

        /*
        |--------------------------------------------------------------------------
        | LẤY NHÀ HÀNG
        |--------------------------------------------------------------------------
        */

        $restaurants = $query->get();

        /*
        |--------------------------------------------------------------------------
        | LOẠI NHÀ HÀNG TRÙNG
        |--------------------------------------------------------------------------
        |
        | Ưu tiên loại theo tên nhà hàng.
        | Ví dụ:
        |
        | Cơm Tấm Sài Gòn
        | Phở Hà Nội
        | KFC
        |
        | sẽ chỉ xuất hiện 1 lần.
        |
        */

        if ($nameColumn) {
            $restaurants = $restaurants
                ->unique(function ($restaurant) use ($nameColumn) {
                    return strtolower(
                        trim($restaurant->{$nameColumn})
                    );
                })
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | CHUẨN HÓA DỮ LIỆU CHO VIEW
        |--------------------------------------------------------------------------
        */

        foreach ($restaurants as $restaurant) {

            // ID nhà hàng
            $restaurant->restaurant_id = $idColumn
                ? $restaurant->{$idColumn}
                : null;

            // Tên
            $restaurant->name = $nameColumn
                ? $restaurant->{$nameColumn}
                : 'Nhà hàng';

            // Địa chỉ
            $restaurant->address = $addressColumn
                ? $restaurant->{$addressColumn}
                : 'Đà Nẵng';

            // Đánh giá
            $restaurant->rating = $ratingColumn
                ? $restaurant->{$ratingColumn}
                : 0;

            // Hình ảnh
            $restaurant->image = $imageColumn
                ? $restaurant->{$imageColumn}
                : '';

            // Thời gian giao
            $restaurant->delivery_time = $deliveryColumn
                ? $restaurant->{$deliveryColumn}
                : '30-45 phút';
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $user = session('user');

        /*
        |--------------------------------------------------------------------------
        | HOME
        |--------------------------------------------------------------------------
        */

        return view('home', [
            'restaurants' => $restaurants,
            'keyword' => $keyword,
            'user' => $user
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TÌM CỘT
    |--------------------------------------------------------------------------
    */

    private function findColumn(array $columns, array $possibleColumns)
    {
        foreach ($possibleColumns as $column) {
            if (in_array($column, $columns)) {
                return $column;
            }
        }

        return null;
    }
}