<?php

namespace App\Libraries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;//引入日志类

class TokenUtil{
    
    public function __construct(){
        parent::__construct();
    }

	public static function getToken(){

        self::token_create();
		Log::debug('==================>get token');
		Log::debug(Session::get('form_token'));
		//return 1;
		return Session::get('form_token');
	}
    
    public static function token_validate(Request $request,$form_token) {
		Log::debug('==================>validate token');
		Log::debug($request->get('form_token'));
		Log::debug(Session::get('form_token'));
         //       return true;
        if (Session::has('form_token')) {
            if($form_token == Session::get('form_token')){
                Session::forget('form_token');
                return true;
            }else{
                return false;
            }
        } else {
                return false;
        }
    }
    
    
    
    /**
     * 生成一个当前的token
     * @param string $form_name
     * @return string
     */
    public static function token_create()
    {
		//return 1;
        $key = self::grante_key();
        $token = md5(substr(time(), 0, 3).$key);
//         Session::forget('form_token');
        Session::put('form_token',$token);
//         var_dump(Session::get('form_token'));

		Log::debug('==================>create token');
		Log::debug(Session::get('form_token'));
        return $token;
    }
    
    /**
     * 验证一个当前的token
     * @param string $form_name
     * @return string
     */
    public static function is_token($token)
    {
        $key = Session::get('form_token');
        $old_token = md5(substr(time(), 0, 3).$key);
        if($old_token == $token)
        {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 删除一个token
     * @param string $form_name
     * @return boolean
     */
    public static function drop_token($form_name)
    {
        Session::forget('form_token');
        return true;
    }
    
    /**
     * 生成一个密钥
     * @return string
     */
    public static function grante_key()
    {
        $encrypt_key = md5(((float) date("YmdHis") + rand(100,999)).rand(1000,9999));
        return $encrypt_key;
    }
}
    
