<?php

use Illuminate\Support\Facades\Response;


class BaseController extends Controller
{
    /**
     * @param $code 错误代码
     * @param $message 错误信息
     * @return array
     */
    protected function error($code, $message)
    {
        return Ressponse::json(array("code" => $code, "message" => $message));
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
