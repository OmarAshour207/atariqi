<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        return view('dashboard.home', compact('admin'));
    }
}
