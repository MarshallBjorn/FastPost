<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function deliveries() {
        return view('admin.deliveries');
    }
}
