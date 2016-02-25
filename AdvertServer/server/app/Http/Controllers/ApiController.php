<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class ApiController extends BaseController
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

    /**
    * 定义响应数据规范
    * 语言:zh[中文简体]、en[英文]
    *
    * @param    string  $code   状态码
    * @param    string  $msg    状态码
    * @param    string  $data   状态码
    * @return   array
    */
    public function response( $code, $msg = '', $data = array() ) {
        $ret = new \stdClass();
        $ret->code  = (int)$code;
        $ret->msg   = (string)$msg;
        $ret->data  = $data;

/*
        $ret = array(
                'code'=>$code,
                'msg'=>$msg,
                'data'=>$data
            );
            */
        return $ret;
    }
}
