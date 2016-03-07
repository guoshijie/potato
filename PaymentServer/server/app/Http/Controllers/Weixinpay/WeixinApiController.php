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


namespace App\Http\Controllers\Weixinpay;    //定义命名空间

use App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Support\Facades\Log;
use App\Libraries\WxPayApi;
use App\Libraries\WxPayConfig;
use App\Libraries\WxPayUnifiedOrder;
use App\Libraries\WxPayDataBase;


class  WeixinApiController extends  ApiController {

	protected $nowTime;
	public function  __construct() {
		$this->nowTime = date('Y-m-d H:i:s');
	}

	/*
	 * 微信预支付接口
	 */
	public function wxPay(Request $request){

		if( $request->has('user_id') && $request->has('out_trade_no') && $request->has('goods_name')  && $request->has('total_fee') ){
			$outTradeNo =   $request->get('out_trade_no');
			$goodsName  =   $request->get('goods_name');
			if($request->get('total_fee') <=1 ){
				$totalFee   = 100;

			}else{
				$totalFee   = (int)$request->get('total_fee') * 100;
			}

			$payment_type   = $request->has('payment_type') ? $request->get('payment_type') : '0';

		}else{
			Log::error(var_export('参数错误', true), array(__CLASS__));
			return $this->response( '10005' );
		}

		//②、统一下单   请求微信预下单
		//②、统一下单   请求微信预下单
		$input = new WxPayUnifiedOrder();

		$input->SetBody($goodsName);
		$input->SetOut_trade_no($outTradeNo);
		$input->SetTotal_fee($totalFee);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 7200));
		$input->SetGoods_tag("test_goods");
		$input->SetAttach($payment_type);
		$input->SetNotify_url($request->root()."
		/weixin/callback");
		$input->SetTrade_type("APP");
		//浏览器测试记得注释掉   $inputObj->SetSpbill_create_ip("1.1.1.1");
		$order = WxPayApi::unifiedOrder($input);
//		debug($_SERVER['REMOTE_ADDR']);
		if(array_key_exists('err_code',$order)){
			return $this->response( '0',$order['err_code'],$order['err_code_des']);
		}


		Log::error(var_export($order, true), array(__CLASS__));
		if($order['return_code']=='SUCCESS'){

			$timestamp = time();
			//参与签名的字段 无需修改  预支付后的返回值
			$arr = array();
			$arr['appid'] = trim(WxPayConfig::APPID);
			$arr['partnerid'] = trim(WxPayConfig::MCHID);
			$arr['prepayid'] = $order['prepay_id'];
			$arr['package'] = 'Sign=WXPay';
			$arr['noncestr'] = $order['nonce_str'];
			$arr['timestamp'] = $timestamp;
			$obj = new WxPayDataBase();
			$obj->SetValues($arr);
			$sign = $obj->SetSign();

			//返回给APP数据
			$data = array();
			$data['return_code']  = $order['return_code'];
			$data['return_msg']   = $order['return_msg'];
			$data['prepay_id']    = $order['prepay_id'];
			$data['trade_type']   = $order['trade_type'];
			$data['nonce_str']    = $order['nonce_str'];
			$data['timestamp']    = $timestamp;
			$data['sign']         = $sign;
			Log::error(var_export($data, true), array(__CLASS__));
			return $this->response( '1','预订单成功',$data );
		}else{
			Log::error(var_export('微信回调错误', true), array(__CLASS__));
			return $this->response( '0' );
		}
	}

}