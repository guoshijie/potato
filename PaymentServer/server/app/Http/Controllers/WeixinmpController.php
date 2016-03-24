<?php
/* *
 * 功能：微信预支付操作
 * 版本：V3
 * 创建日期：2015-11-18
 * 作者：liangfeng@shinc.net
 * 说明：
 * 微信手机APP支付类型的预支付实现，与支付宝的业务逻辑不同，微信是需要在web服务器端实现预付款回传给APP端
 * APP根据服务器回传的内容去实现微信支付，然后再通过微信回调通知给web服务器端。
 *
 * 先生成一笔订单，然后根据回调通知来更新这笔订单是否已付款。
 *
 */


namespace App\Http\Controllers;    //定义命名空间

use App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Libraries\Curl;
// -- 微信公众号支付
use App\Libraries\Weixin\WxPayApi;
use App\Libraries\Weixin\JsApiPay;
use App\Libraries\Weixin\WxPayConfig;
use App\Libraries\Weixin\WxPayUnifiedOrder;
use App\Libraries\Weixin\WxPayDataBase;
use App\Libraries\Weixin\WxPayNotifyReply;
use App\Libraries\Weixin\WxPayOrderQuery;

/*
// -- 微信公众号支付
use App\Libraries\Weixin\WxPayApi		AS WxPayApi_mp;
use App\Libraries\Weixin\WxPayConfig	AS WxPayConfig_mp;
use App\Libraries\Weixin\WxPayUnifiedOrder AS WxPayUnifiedOrder_mp;
use App\Libraries\Weixin\WxPayDataBase AS WxPayDataBase_mp;
use App\Libraries\Weixin\WxPayNotifyReply AS WxPayNotifyReply_mp;
use App\Libraries\Weixin\WxPayOrderQuery AS WxPayOrderQuery_mp;
 */
use App\Http\Models\WeixinModel;
use App\Http\Models\AlipayModel;

class  WeixinmpController extends  ApiController {

	protected $nowTime;
	protected $_model;
	public function  __construct() {
		$this->nowTime = date('Y-m-d H:i:s');
		$this->_model = new WeixinModel();
	}

	/*
	 * 微信预支付接口
	 */
	public function pay(Request $request){
		Log::info('----------- weixin pay -------------');
		// 客户端ip
		$_SERVER['REMOTE_ADDR'] = $request->REMOTE_ADDR;
		$messages = $this->vd([
			'user_id' => 'required',
			'out_trade_no' => 'required',
			'body' => 'required',
			'total_fee' => 'required',
			'notify_url' => 'required',  // 回调地址
			'code' => 'required',  // jsapi 获取openid 需要的参数
			],$request);
		if($messages!='') return $this->response(10005, $messages);

		$outTradeNo =   $request->get('out_trade_no');
		$body  =   $request->get('body');
		// 金额单位是分，最小1分，不支持0.01元，
		$totalFee   = $request->get('total_fee') * 100;

		$payment_type   = $request->has('payment_type') ? $request->get('payment_type') : '0';
		$notify_url		= $request->get('notify_url'); // 回调地址

		//①、获取用户openid 
		// 网页端必须提交code参数
		$tools = new JsApiPay();
		$code = $request->get('code');
		$cacheid = 'weixin_mp_code_'.$code;
		if(Cache::has($cacheid)){
			$openId = Cache::get($cacheid);
		}else{
			$openId = $tools->GetOpenid();
			Cache::put($cacheid, $openId, 60*24*30);
		}

		//②、统一下单   请求微信预下单
		$input = new WxPayUnifiedOrder();

		$input->SetBody($body);
		$input->SetAttach($payment_type);
		$input->SetOut_trade_no($outTradeNo);
		$input->SetTotal_fee($totalFee);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 7200));
		//$input->SetGoods_tag("test_goods");
		$input->SetNotify_url($notify_url);

		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);

		//浏览器测试记得注释掉   $inputObj->SetSpbill_create_ip("1.1.1.1");
		$order = WxPayApi::unifiedOrder($input);
		if(array_key_exists('err_code',$order)){
			return $this->response( '0',$order['err_code'],$order['err_code_des']);
		}

		Log::info('------ $order ---');
		Log::info(var_export($order, true), array(__CLASS__));
		if($order['return_code']=='SUCCESS'){
			$timestamp = time();

			$jsApiParameters = $tools->GetJsApiParameters($order);
			//获取共享收货地址js函数参数
			$editAddress = $tools->GetEditAddressParameters();
			$data['jsApiParameters'] = $jsApiParameters;
			$data['editAddress'] = $editAddress;

			Log::info('--- $jsApiParameters ---');
			Log::info($jsApiParameters);
			Log::info($editAddress);

			Log::info('------ 预订单成功 ---');
			//Log::info(var_export($data, true), array(__CLASS__));
			return $this->response( '1','预订单成功',$data );
		}else{
			Log::info(var_export(' --- 微信回调错误', true), array(__CLASS__));
			return $this->response( '0' );
		}
	}


	/*
	 * 回调
	 */
	public function callback(Request $request){
		Log:info('--------Weixin callback  ---- ');
		//获取回调通知xml
		//$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : $request->get('HTTP_RAW_POST_DATA');
		Log::info('--- $xml ----');
		Log::info(var_export($xml, true), array(__CLASS__));
		$reply = new WxPayNotifyReply();
		$data = $reply->FromXml($xml);
		Log::info('--- $data ----');
		Log::info(var_export($data, true), array(__CLASS__));
		file_put_contents('log.txt',"\n\n红包回调通知".print_r($data,1),FILE_APPEND);

		if(!$data){
			Log::info(var_export('非法请求', true), array(__CLASS__));
			return $this->response( '10006' );
		}

		$return_code = $data['return_code'];

		if($return_code=='FAIL'){
			$err_code_des = $data['err_code_des'];
			Log::info(var_export('异步回调通知错误FAIL', true), array(__CLASS__));
			return $this->response( '0', $err_code_des);
		}

		if($return_code=='SUCCESS'){
			//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
			//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
			//尽可能提高通知的成功率，但微信不保证通知最终能成功。
			$transaction_id = $data['transaction_id'];

			$input = new WxPayOrderQuery();
			$input->SetTransaction_id($transaction_id);
			$result = WxPayApi::orderQuery($input);

			if(array_key_exists("return_code", $result)
				&& array_key_exists("result_code", $result)
				&& $result["return_code"] == "SUCCESS"
				&& $result["result_code"] == "SUCCESS")
			{
				$total_fee = $data['total_fee'] / 100;
				//插入数据库
				$weixinData = array(
					'transaction_id'   =>   $data['transaction_id'],
					'out_trade_no'     =>   $data['out_trade_no'],
					'total_fee'        =>   $total_fee,
					'nonce_str'        =>   $data['nonce_str'],
					'sign'             =>   $data['sign'],
					'create_time'      =>   $this->nowTime,
					'time_expire'      =>   date("Y-m-d H:i:s", time() + 7200),
					'time_end'         =>   $data['time_end'],
					'is_subscribe'     =>   $data['is_subscribe'],
					'trade_type'       =>   $data['trade_type'],
					'bank_type'        =>   $data['bank_type'],
					'fee_type'         =>   $data['fee_type'],
					'cash_fee'         =>   $data['cash_fee'],
					'appid'            =>   $data['appid'],
					'mch_id'           =>   $data['mch_id'],
					'openid'           =>   $data['openid'],
					'return_code'      =>   $data['return_code'],
				);

				$trade_status = $data['return_code'];
				$out_trade_no = $data['out_trade_no'];
				$pay_amount   = $total_fee;
				$redpacket    = $data['attach'];


				$flag = false;
				if ($trade_status == 'SUCCESS') {
					$weixinInfo = $this->_model->load($transaction_id);
					if (empty($weixinInfo)) {
						$this->_model->add($weixinData);
						$flag = true;
					} else {
						$db_status = $weixinInfo->return_code;
						if ($db_status != $trade_status) {
							$param = [
								'return_code' => $trade_status,
								'create_time' => $this->nowTime
							];
							$this->jnlWeixinModel->update( $transaction_id, $param);
						}
					}
				}

				try {
					if ($flag) {
						$alipayM = new AlipayModel();
						$data = $alipayM->payCallbackUpdateJnl($out_trade_no, $pay_amount , $redpacket);
						//$data = $this->_model->payCallbackUpdateJnl($out_trade_no, $pay_amount , $redpacket);

						if(!$data){
							return "fail";
						}
					}

				} catch (\Exception $e) {
					Log::info(var_export($e, true), array(__CLASS__));
				}

				return 'SUCCESS';
			}
			return 'FAIL';


		} else {
			Log::info('------ Weixin FAIL ---- ');
			//验证失败
			return 'FAIL';

		}
	}

	/*
	 * 获取 access_token
	 */
	private function getAccessToken(){
		// 获取缓存的access_token
		if( Cache::has('weixin_access_token')){  // cache 要存到公用存储器上，比如db，redis
			$access_token_obj = Cache::get('weixin_access_token');
			// 超过有效期; 减少5秒解决服务器可能延迟问题
			if( !isset($access_token_obj->access_token) || $access_token_obj->expire_time + 5 < time() ){
				unset($access_token_obj);
			}
		}

		// 重新获取access_token
		if( !isset($access_token_obj)){
			$access_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WxPayConfig::APPID.'&secret='.WxPayConfig::APPSECRET;
			$curl = new Curl();
			$access_token_json = $curl->get($access_token_url);
			$access_token_obj = json_decode($access_token_json);
			if(isset($access_token_obj->access_token)){
				$access_token_obj->expire_time = time() + $access_token_obj->expires_in;
				Cache::put('weixin_access_token', $access_token_obj, $access_token_obj->expires_in/60);
			}else{
				Log::info('--- error: weixin access_token get fail ---'.$access_token_json);
			}
		}
		return $access_token_obj->access_token;
	}

	/* 获取tickit
	 * 
	 */
	private function getJsApiTicket(){
		$access_token = $this->getAccessToken();
		// 获取缓存的access_token
		if( Cache::has('weixin_ticket')){  // cache 要存到公用存储器上，比如db，redis
			$tickit_obj = Cache::get('weixin_ticket');
			// 超过有效期; 减少5秒解决服务器可能延迟问题
			if( $tickit_obj->expire_time + 5 < time() ){
				unset($tickit_obj);
			}
		}

		if( !isset($tickit_obj)){
			$tickit_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
			$curl = new Curl();
			$tickit_json = $curl->get($tickit_url);
			$tickit_obj = json_decode($tickit_json);
			if($tickit_obj->errcode==0){
				$tickit_obj->expire_time = time() + $tickit_obj->expires_in;
				Cache::put('weixin_ticket', $tickit_obj, $tickit_obj->expires_in/60);
			}else{
				Log::info('--- error: weixin tickit get fail ---'.$tickit_json);
			}
		}
		return $tickit_obj->ticket;
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	/*
	 * 获取signature
	 */
	public function sign(Request $request) {
		$messages = $this->vd([
			'url' => 'required',
			],$request);
		if($messages!='') return $this->response(10005, $messages);

		$url = $request->get('url');
		$url = urldecode($url);

		$jsapiTicket = $this->getJsApiTicket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		//$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		//$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
			"appId"     => WxPayConfig::APPID,
			"nonceStr"  => $nonceStr,
			"timestamp" => (string)$timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage; 
	}
	

}
