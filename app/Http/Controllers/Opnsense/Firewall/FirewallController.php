<?php

namespace App\Http\Controllers\Opnsense\Firewall;

use App\Http\Controllers\Controller;

class FirewallController extends Controller
{

    public function index(){
        return view('firewall.index');
    }

}