<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GdsController extends Controller
{
    public function setupGds(){
        return view('setup_gds');
    }
}
