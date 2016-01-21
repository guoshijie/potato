<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get(Request $req)
    {
        $id = $req->get("id");
        return $this->success(array(
            "user" => array(
                "id" => $id,
                "name" => "名称"
            )
        ));
    }

    public function create(Request $req)
    {
        $id = $req->get("id");
        $name = $req->get("name");
        return $this->success(array(
            "user" => array(
                "id" => $id,
                "name" => $name
            )
        ));
    }
}
