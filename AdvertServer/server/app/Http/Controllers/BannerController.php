<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BannerController extends Controller
{

    public function get()
    {
        
        return $this->success(array(
            "name"  => 'banner'
        ));
    }
}
