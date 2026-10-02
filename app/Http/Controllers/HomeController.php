<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword', '');

        $query = DB::table('restaurants')
            ->where('status', 'open');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('address', 'like', '%' . $keyword . '%');
            });
        }

        $restaurants = $query
            ->orderByDesc('rating')
            ->get();

        $user = session('user');

        return view('home', [
            'restaurants' => $restaurants,
            'keyword' => $keyword,
            'user' => $user,
        ]);
    }
}