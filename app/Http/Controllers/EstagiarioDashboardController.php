<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstagiarioDashboardController extends Controller
{
    public function index()
    {
        return view('estagiario.dashboard');
    }
}
