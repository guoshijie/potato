<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @param $code 错误代码
     * @param $message 错误信息
     * @return array
     */
    protected function error($code, $message)
    {
        return array("code" => $code, "message" => $message);
    }

    /**
     * @param array $data 结果返回数据
     * @return array
     */
    protected function success(array $data = array())
    {
        return array("code" => 200, "data" => $data);
    }
}
