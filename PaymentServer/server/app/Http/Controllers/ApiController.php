<?php
/*
 * Lumen微框架Api接口开发基类
 * 安全验证、登录验证、错误机制、response响应机制、日记记录
 * from  :www.sexyphp.com
 * author:lianger
 */
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends BaseController
{

    /*
	|--------------------------------------------------------------------------
	| Default Api Controller
	|--------------------------------------------------------------------------
	| API控制器，所有接口的父类。用于通用的验证和数据处理
	|
	*/

    public $time;		//时间变量，存储时间datetime： 2016-02-21 12：12：12
    public $error;		//报错数组，存储通用的和常规报错参数
    protected $shid;	//id
    protected $lang="zh";	//获取接口语言
    protected $response;

    const SUCCESSCODE	= 1;
    const FAILDCODE		= 0;
    const SUCCESS		= '成功';
    const FAILD			= '失败';


    public function __construct(Request $request){
        $this->time = date('Y-m-d H:i:s', time());
        $this->error = $this->getErrorList();

        $lang = '';

        if( $request->has( 'lang' ) ) {
            $lang = $request->has( 'lang' );
            $lang = trim( $lang );
        }

        // 真实用户登录，把过期时间设长
        if($request->hasSession('user') && Session::has('user.is_real') && $request->getSession('user.is_real')){
//            Config::set('session.lifetime', 43200);
        }
        if($request->hasSession('user') && strpos($request->getSession('user.tel'), 'a')===false){
//            Config::set('session.lifetime', 43200);
        }

        $this->lang = ( 'en'==$lang ) ? $lang : 'zh';
    }

    /*
	 * 是否登陆
	 */
    protected function isLogin(Request $request){
        return $request->hasSession('user') ? true : false;
    }


    /*
	 * 定义通用报错列表
	*
	* @return	array
	*/
    public function getErrorList(){

        return array(

            0=>array("en"=>"Failed", "zh"=>"失败"),
            1=>array("en"=>"Success", "zh"=>"成功"),

            /*
			|--------------------------------------------------------------------------
			| 系统级错误
			|--------------------------------------------------------------------------
			|
			| 系统级错误
			|
			*/
            10001=>array("en"=>"System error", "zh"=>"系统错误"),
            10002=>array("en"=>"Service unavailable", "zh"=>"服务暂停"),
            10003=>array("en"=>"Remote service error", "zh"=>"远程服务错误"),
            10004=>array("en"=>"IP limit", "zh"=>"IP限制"),
            10005=>array("en"=>"Param error", "zh"=>"参数错误"),
            10006=>array("en"=>"Illegal request", "zh"=>"非法请求"),
            10007=>array("en"=>"Request api not found", "zh"=>"接口不存在"),
            10008=>array("en"=>"HTTP method error", "zh"=>"请求方式错误"),
            10009=>array("en"=>"Request body length over limit", "zh"=>"请求长度超过限制"),
            10010=>array("en"=>"Invalid user", "zh"=>"不合法的用户"),
            10011=>array("en"=>"User requests out of rate limit", "zh"=>"用户请求频次超过上限"),
            10012=>array("en"=>"Request timeout", "zh"=>"请求超时"),
            10013=>array("en"=>"User doesn't exists", "zh"=>"用户不存在"),
            10014=>array("en"=>"Username has registered", "zh"=>"用户名已注册"),
            10015=>array("en"=>"No phone number","zh"=>"无电话号码"),
            10016=>array("en"=>"User has login","zh"=>"用户已登录"),
            10017=>array("en"=>"exit login fail","zh"=>"退出登录失败"),
            10018=>array("en"=>"User has not login","zh"=>"用户未登录"),
            10019=>array("en"=>"create token Failed","zh"=>"令牌未生成"),
            10020=>array("en"=>"User has not token","zh"=>"令牌无效"),
            10021=>array("en"=>"User has not token","zh"=>"交易失败"),
            10022=>array("en"=>"User has not registered", "zh"=>"用户未注册"),
            10023=>array("en"=>"Param limit error", "zh"=>"参数格式错误"),
            /*
			|--------------------------------------------------------------------------
			| 服务级错误
			|--------------------------------------------------------------------------
			|
			| 服务级错误
			| 2[级别]01[模块]01[错误编号]
			|
			*/
            //20000 - 20099   Common error    公共错误
            20001=>array("en"=>"Unknown error", "zh"=>"未知错误"),
            20002=>array("en"=>"DB error", "zh"=>"数据库错误"),
            20003=>array("en"=>"Object already exists", "zh"=>"记录已存在"),
            //20100 - 20199   System model error    系统模块错误
            20101=>array("en"=>"Cid parameter is null", "zh"=>"Cid参数为null"),
            20102=>array("en"=>"Failed to initialize user data", "zh"=>"初始化用户数据失败"),
            //20200 - 20299   User model error    用户模块错误
            20201=>array("en"=>"Uid parameter is null", "zh"=>"Uid参数为null"),
            20202=>array("en"=>"Username or password error","zh"=>"用户名或密码错误"),
            20203=>array("en"=>"Username and pwd auth out of rate limit", "zh"=>"用户名密码认证超过请求限制"),
            20204=>array("en"=>"Accounts have been locked", "zh"=>"账户已被锁定"),
            20205=>array("en"=>"Failed to modify password", "zh"=>"修改密码失败"),
            20206=>array("en"=>"The phone number has been used", "zh"=>"该手机号已经被使用"),
            20207=>array("en"=>"The account has bean bind phone", "zh"=>"该用户已经绑定手机"),
            20208=>array("en"=>"Verification code error", "zh"=>"验证码错误"),
            20209=>array("en"=>"Failed to send verification code", "zh"=>"发送验证码失败"),
            20210=>array("en"=>"Verification code ok", "zh"=>"验证码正确"),
            20212=>array("en"=>"The phone only bind once", "zh"=>"该手机号码已绑定收货地址"),
            20211=>array("en"=>"This address is not exists", "zh"=>"该收货地址不存在"),
            20212=>array("en"=>"Please choice address", "zh"=>"请选择收货地址"),
            //20300 - 20399   Article model error    文章模块错误
            20301=>array("en"=>"Aid parameter is null", "zh"=>"Aid参数为null"),
            20302=>array("en"=>"Content is null", "zh"=>"内容为空"),
            20303=>array("en"=>"Article not found", "zh"=>"文章不存在"),
            20350=>array("en"=>"Article category error", "zh"=>"文章分类错误"),
            20351=>array("en"=>"Caid parameter is null", "zh"=>"Caid参数为null"),
            //20400 - 20499   Comment model error    评论模块错误
            20401=>array("en"=>"Coid parameter is null", "zh"=>"Coid参数为null"),
            20402=>array("en"=>"Comment does not exist", "zh"=>"不存在的评论"),
            20403=>array("en"=>"Illegal comment", "zh"=>"不合法的评论"),
            //20500 - 20599   Share model error    分享模块错误
            20501=>array("en"=>"You had get the red envelopes", "zh"=>"您已领取过该红包"),
            20502=>array("en"=>"Red envelopes for failure, please try again later", "zh"=>"红包领取失败,请稍后再试"),
            20503=>array("en"=>"Congratulations, red envelopes for success", "zh"=>"恭喜，红包领取成功"),
            20504=>array("en"=>"Phone number format is not correct", "zh"=>"手机号格式不正确"),
            20505=>array("en"=>"Mobile phone number can't be empty", "zh"=>"手机号码不能为空"),

            /*
			|--------------------------------------------------------------------------
			| 购买夺宝错误
			|--------------------------------------------------------------------------
			|
			| 业务级错误
			| 3[级别]01[模块]01[错误编号]
			|
			*/
            30000=>array("en"=>"", "zh"=>"购买失败"),
            30001=>array("en"=>"", "zh"=>"本期已下线"),
            30002=>array("en"=>"", "zh"=>"用户不存在"),
            30003=>array("en"=>"", "zh"=>"剩余次数不足"),
            30004=>array("en"=>"", "zh"=>"数据库操作失败"),


            /*
			|--------------------------------------------------------------------------
			| 订单错误
			|--------------------------------------------------------------------------
			|
			| 业务级错误
			| 4[级别]01[模块]01[错误编号]
			|
			*/
            40001=>array("en"=>"", "zh"=>"库存数量不足"),
            40002=>array("en"=>"", "zh"=>"检测到不存在商品，用户不良行为"),
            40003=>array("en"=>"", "zh"=>"购物车已满30笔，请先删除多余订单"),
            40004=>array("en"=>"", "zh"=>"请选择商品"),
            40005=>array("en"=>"", "zh"=>"请填写发票信息"),
            40006=>array("en"=>"", "zh"=>"请先填写收货信息"),
            40007=>array("en"=>"", "zh"=>"请输入订单状态"),
        );
    }


//    public function index(){
//        return $this->response(200,'test');
//    }


    /**
     * 定义响应数据规范
     * 语言:zh[中文简体]、en[英文]
     *
     * @param 	string	$code 	状态码
     * @param 	string	$msg 	状态码
     * @param 	string	$data 	状态码
     * @return	array
     */
    public function response( $code, $msg = null, $data = array() ) {
        $code = (int)$code;
        if( null == $msg ) {
            $errList = $this->getErrorList();

            if( !array_key_exists( $code, $errList ) ) {
                return 'key not exist in config';
            }
            $msg = $errList[ $code ][ $this->lang ];

        }

        $ret = array(
            'code' => $code,
            'msg' => "{$msg}",
        );

        if( null != $data ) {
            $ret[ 'data' ] = $data;
        }

        return $ret;
    }


    /**
     * 定义响应数据规范
     * 语言:zh[中文简体]、en[英文]
     *
     * @param 	string	$code 	状态码
     * @param 	string	$msg 	状态码
     * @param 	string	$data 	状态码
     * @return	array
     */
    public function getPageResponse( $code, $msg = null, $data = array() , $page ) {
        $code = (int)$code;
        if( null == $msg ) {
            $errList = $this->getErrorList();

            if( !array_key_exists( $code, $errList ) ) {
                return 'key not exist in config';
            }

            $msg = $errList[ $code ][ $this->lang ];

        }

        $ret = array(
            'code' => $code,
            'msg' => "{$msg}",
        );

        if( null != $data ) {
            $ret[ 'data' ] = $data;
        }

        if( null != $page ) {
            $ret[ 'pageInfo' ] = $page;
        }

        return $ret;
    }

	/*
	 *	 *	 validate
	 *		 *
	 *			 * */
	protected function vd($rules, $request, $tip=array()){
		// 自定义错误信息，不谢用默认的
		$selfMessages = array(
			'required' => '请填写 :attribute ;',
			'same'    => 'The :attribute and :other must match;',
			'size'    => 'The :attribute must be exactly :size;',
			'between' => 'The :attribute must be between :min - :max;',
			'in'      => 'The :attribute must be one of the following types: :values ;',
		);

		foreach($rules as $k=>$vr){
			if(isset($tip[$k])){
				$selfMessages[$k.'.'.$vr] = str_replace(':attribute', $tip[$k], $selfMessages[$vr]);
			}
		}

		$validate = Validator::make($request->all(), $rules,$selfMessages);
		$messages = $validate->messages()->all();
		return implode(' & ', $messages);
	}

}
