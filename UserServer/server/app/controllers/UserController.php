<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;


class UserController extends BaseController
{
    public function get()
    {
        $id = Input::get("id");
        return $this->success(array(
            "user" => array(
                "id" => $id,
                "name" => "名称"
            )
        ));
    }

    public function create()
    {
        $id = Input::get("id");
        $name = Input::get("name");
        return $this->success(array(
            "user" => array(
                "id" => $id,
                "name" => $name
            )
        ));
    }
}
