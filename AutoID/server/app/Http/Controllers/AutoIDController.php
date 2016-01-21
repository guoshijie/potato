<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutoIDController extends Controller
{

    public function get(Request $req)
    {
        $type = $req->input("type");
        //从redis中incr 结果返回获取
        //$id = redis.incr("AutoId:".$type);
        $id = 1;
        return $this->success(array(
            "id" => $id,
            "type" => $type
        ));
    }
}
