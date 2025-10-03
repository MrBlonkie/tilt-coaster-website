<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AutoControlController extends Controller
{
    private $espIp;

    public function __construct()
    {
        $this->espIp = env('ESP_IP');
    }

    public function index()
    {
        return view('auto-control');
    }

}


