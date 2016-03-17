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
use App\Libraries\WxPayApi;
use App\Libraries\WxPayConfig;
use App\Libraries\WxPayUnifiedOrder;
use App\Libraries\WxPayDataBase;
use App\Libraries\WxPayNotifyReply;
use App\Libraries\WxPayOrderQuery;
use App\Http\Models\Weixin\WeixinPayModel;


class  WeixinController extends  ApiController {

	protected $nowTime;
	protected $_model;
	public function  __construct() {
		$this->nowTime = date('Y-m-d H:i:s');
		$this->_model = new WeixinPayModel();
	}

	/*
	 * 微信预支付接口
	 */
	public function pay(Request $request){
		Log::error('----------- weixin pay -------------');
		$messages = $this->vd([
			'user_id' => 'required',
			'out_trade_no' => 'required',
			'body' => 'required',
			'total_fee' => 'required',
			],$request);
		if($messages!='') return $this->response(10005, $messages);

		$outTradeNo =   $request->get('out_trade_no');
		$body  =   $request->get('body');
		if($request->get('total_fee') <=1 ){ // 金额单位是分，最小1分，不支持0.01元，
			$totalFee   = 100;

		}else{
			$totalFee   = (int)$request->get('total_fee') * 100;
		}

		$payment_type   = $request->has('payment_type') ? $request->get('payment_type') : '0';


		//②、统一下单   请求微信预下单
		//②、统一下单   请求微信预下单
		$input = new WxPayUnifiedOrder();

		$input->SetBody($body);
		$input->SetOut_trade_no($outTradeNo);
		$input->SetTotal_fee($totalFee);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 7200));
		$input->SetGoods_tag("test_goods");
		$input->SetAttach($payment_type);
		$input->SetNotify_url($request->root()."/weixin/callback");
		$input->SetTrade_type("APP");
		//浏览器测试记得注释掉   $inputObj->SetSpbill_create_ip("1.1.1.1");
		$order = WxPayApi::unifiedOrder($input);
//		debug($_SERVER['REMOTE_ADDR']);
		if(array_key_exists('err_code',$order)){
			return $this->response( '0',$order['err_code'],$order['err_code_des']);
		}


		Log::error('------ $order ---');
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
			Log::error('------ 预订单成功 ---');
			//Log::error(var_export($data, true), array(__CLASS__));
			return $this->response( '1','预订单成功',$data );
		}else{
			Log::error(var_export(' --- 微信回调错误', true), array(__CLASS__));
			return $this->response( '0' );
		}
	}


	/*
	 * 回调
	 */
	public function callback(){
		//获取回调通知xml
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		Log:error('--------Weixin callback  ---- '.);
		Log::error('--- $xml ----');
		Log::error(var_export($xml, true), array(__CLASS__));
		$reply = new WxPayNotifyReply();
		$data = $reply->FromXml($xml);
		Log::error('--- $data ----');
		Log::error(var_export($data, true), array(__CLASS__));
		file_put_contents('log.txt',"\n\n红包回调通知".print_r($data,1),FILE_APPEND);

		if(!$data){
			Log::error(var_export('非法请求', true), array(__CLASS__));
			return $this->response( '10006' );
		}

		$return_code = $data['return_code'];

		if($return_code=='FAIL'){
			$err_code_des = $data['err_code_des'];
			Log::error(var_export('异步回调通知错误FAIL', true), array(__CLASS__));
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

						$data = $this->_model->payCallbackUpdateJnl($out_trade_no, $pay_amount , $redpacket);

						if(!$data){
							return "fail";
						}
					}

				} catch (\Exception $e) {
					Log::error(var_export($e, true), array(__CLASS__));
				}

				return 'SUCCESS';
			}
			return 'FAIL';


		} else {
			Log::error('------ Weixin FAIL ---- ');
			//验证失败
			return 'FAIL';

		}
	}

}
