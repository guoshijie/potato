<?php
/***
 *支付宝异步回调
 *@author  zhaozhonglin@shinc.net
 *
 *@version  v1.0
 *@copyright shinc
 */
namespace App\Http\Controllers;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Http\Response;            //输入输出类
use Illuminate\Support\Facades\Log;
use App\Libraries\AlipayNotify;//引入支付宝移动支付异步服务器支付宝扩展
use App\Libraries\alipayConfig;//引入支付宝移动支付异步服务器配置文件
use App\Http\Models\AlipayModel;

class AlipayController extends  ApiController {

	protected $_model;
	public function  __construct() {
		$this->_model = new AlipayModel();
	}

	public function callback(Request $request){

		//计算得出通知验证结果
		$alipayC  = new alipayConfig();
		$alipay_config = $alipayC->config();
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		Log::info('支付宝签名验证状态:' . var_export($verify_result,true),array(__CLASS__));
		Log::info('支付宝回调:' . var_export($request->all(),true),array(__CLASS__));
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代


			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

			//商户订单号

			$data = array(
				'trade_no'      => $request->get('trade_no'),
				'notify_type'   => $request->get('notify_type'),
				'notify_id'     => $request->get('notify_id'),
				'sign_type'     => $request->get('sign_type'),
				'sign'          => $request->get('sign'),
				'notify_time'   => $request->get('notify_time'),
				'out_trade_no'  => $request->get('out_trade_no'),
				'subject'       => $request->get('subject'),
				'payment_type'  => $request->get('payment_type'),
				'trade_status'  => $request->get('trade_status'),
				'seller_id'     => $request->get('seller_id'),
				'seller_email'  => $request->get('seller_email'),
				'buyer_id'      => $request->get('buyer_id'),
				'buyer_email'   => $request->get('buyer_email'),
				'total_fee'     => $request->get('total_fee'),
				'quantity'      => $request->get('quantity'),
				'price'         => $request->get('price'),
				'body'          => $request->get('body'),
				'gmt_create'    => $request->get('gmt_create'),
				'gmt_payment'   => $request->get('gmt_payment'),
				'is_total_fee_adjust' => $request->get('is_total_fee_adjust'),
				'use_coupon'    => $request->get('use_coupon'),
				'discount'      => $request->get('discount'),
				'refund_status' => '',//退款状态
				'gmt_refund'    => '');//退款时间

			$out_trade_no   = $data['out_trade_no'];
			$trade_no       = $data['trade_no'];
			$trade_status   = $data['trade_status'];
			$pay_amount     = $data['total_fee'];

			$arrPay_type    = explode('=',$request->get('body'));
			$pay_type       = $arrPay_type[1];
			Log::info('-------- pay_type:'.$pay_type);


			//数据处理
			$flag = false;
			if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {  //异步成功处理
				$alipayInfo = $this->_model->load($trade_no);

				//debug($alipayInfo);
				if (empty($alipayInfo)) {
					$this->_model->add($data);
					$flag = true;
				} else {
					$db_status = $alipayInfo->trade_status;
					if ($db_status != $trade_status) {
						$param = [
							'trade_status' => $trade_status,
							'notify_time' => $request->get('notify_time')
						];
						$this->_model->update($trade_no, $param);
					}

					$flag = true;
				}
			}

			try {
				if ($flag) {

					$data = $this->_model->payCallbackUpdateJnl($out_trade_no, $pay_amount , $pay_type);

					if(!$data){
						$flag = false;
					}
				}

			} catch (\Exception $e) {
				Log::error(var_export($e, true), array(__CLASS__));
			}
			 Log::info('----flag:'.$flag);
			if($flag){
				return $this->response(1, 'success');
			}else{
				return $this->response(1, 'fail');
			}

			//return 'success';
		}
		else
		{
			//验证失败
			//return "fail";
			 Log::info('----flag:fail');
			return $this->response(1, 'fail');

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}


	/*
	 * 支付结果
	 */
	public function result(Request $request){
		$messages = $this->vd([
			'out_trade_no' => 'required',
			'pay_type' => 'required',
			],$request);
		if($messages!='') return $this->response(10005, $messages);

		$out_trade_no	= $request->get('out_trade_no');
		$pay_type	= $request->get('pay_type');

		$res =  $this->_model->getResult($out_trade_no, $pay_type);
		if($res){
			return $this->response(1,'成功',$res);
		}else{
			return $this->response(0,'未找到订单');
		}
	}

}
