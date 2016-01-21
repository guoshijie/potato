<?php

use Illuminate\Support\Facades\Input;

class AutoIDController extends BaseController
{
    public function get()
    {
        $type = Input::get("type");

        //从redis中incr 结果返回获取
        //$id = redis.incr("AutoId:".$type);
        $id = 1;
        return $this->success(array(
            "id" => $id,
            "type" => $type
        ));
    }
}
