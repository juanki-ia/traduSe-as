<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovilController extends Controller
{
    public function camara()
    {
        return view('movil.camara');
    }
}
