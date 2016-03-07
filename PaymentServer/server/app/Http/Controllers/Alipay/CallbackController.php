<?php
/***
 *支付宝异步回调
 *@author  zhaozhonglin@shinc.net
 *
 *@version  v1.0
 *@copyright shinc
 */
namespace App\Http\Controllers\Alipay;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Support\Facades\Log;
use App\Libraries\AlipayNotify;//引入支付宝移动支付异步服务器支付宝扩展
use App\Libraries\alipayConfig;//引入支付宝移动支付异步服务器配置文件
use App\Http\Models\Alipay\AlipayModel;

class CallbackController extends  ApiController {

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

			$out_trade_no   = $verify_result['out_trade_no'];
			$trade_no       = $verify_result['trade_no'];
			$trade_status   = $verify_result['trade_status'];
			$pay_amount     = $verify_result['total_fee'];
			$payment_type   = $verify_result['payment_type'];


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

					$data = $this->_model->payCallbackUpdateJnl($out_trade_no, $pay_amount , $payment_type);

					if(!$data){
						return "fail";
					}
				}

			} catch (\Exception $e) {
				Log::error(var_export($e, true), array(__CLASS__));
			}

			//return 'success';
			return $this->response(1);
		}
		else
		{
			//验证失败
			//return "fail";
			return $this->response(0);

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}

	public function getTest(Request $request){
		if($request) {
			$verify_result = Array(
				'discount'            => 0.00,
				'payment_type'        => 1,
				'subject'             => 'iPhone6 4.7英寸 128G',
				'trade_no'            => '2015101300001000340065',
				'buyer_email'         => 13240372487,
				'gmt_create'          => '2015-10-13 19:43:03',
				'gmt_payment'         => '2015-10-13 19:43:03',
				'notify_type'         => 'trade_status_sync',
				'quantity'            => 1,
				'notify_id'           => '',
				'out_trade_no'        => 'G304714535525622',
				'seller_id'           => 2088911708976095,
				'notify_time'         => '2015-10-13 19:43:04',
				'body'                => 2,
				'trade_status'        => 'TRADE_SUCCESS',
				'is_total_fee_adjust' => 'N',
				'total_fee'           => '12160.00',
				'seller_email'        => 'admin@shinc.net',
				'price'               => 0.01,
				'buyer_id'            => 2088712044133340,
				'use_coupon'          => 'N',
				'sign_type'           => 'RSA',
				'sign'                => 'mVd5lMtnAXEhSjv7ZQfQjeVkTH8kJGm2Hj/9CbZwAf32Us//aiDFSn9xmzlYQIcAt/HsJmMb/dU/FaTkXoBeMB21z+RPcYiizLjtpaxjgEhr75O9ESZVbxzLqiBxAh2J7eieBYofd4P03+PeQNZyVZV2Xm7jhi/t5cqMfVUZp8A='
			);



			$data = array(
				'trade_no'      => $verify_result['trade_no'],
				'notify_type'   => $verify_result['notify_type'],
				'notify_id'     => $verify_result['notify_id'],
				'sign_type'     => $verify_result['sign_type'],
				'sign'          => $verify_result['sign'],
				'notify_time'   => $verify_result['notify_time'],
				'out_trade_no'  => $verify_result['out_trade_no'],
				'subject'       => $verify_result['subject'],
				'payment_type'  => $verify_result['payment_type'],
				'trade_status'  => $verify_result['trade_status'],
				'seller_id'     => $verify_result['seller_id'],
				'seller_email'  => $verify_result['seller_email'],
				'buyer_id'      => $verify_result['buyer_id'],
				'buyer_email'   => $verify_result['buyer_email'],
				'total_fee'     => $verify_result['total_fee'],
				'quantity'      => $verify_result['quantity'],
				'price'         => $verify_result['price'],
				'body'          => $verify_result['body'],
				'gmt_create'    => $verify_result['gmt_create'],
				'gmt_payment'   => $verify_result['gmt_payment'],
				'is_total_fee_adjust' => $verify_result['is_total_fee_adjust'],
				'use_coupon'    => $verify_result['use_coupon'],
				'discount'      => $verify_result['discount'],
				'refund_status' => '',//退款状态
				'gmt_refund'    => ''
			);//退款时间

			$out_trade_no   = $verify_result['out_trade_no'];
			$trade_no       = $verify_result['trade_no'];
			$trade_status   = $verify_result['trade_status'];
			$pay_amount     = $verify_result['total_fee'];
			$payment_type   = $verify_result['payment_type'];


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

					$data = $this->_model->payCallbackUpdateJnl($out_trade_no, $pay_amount , $payment_type);

					if(!$data){
						return "fail";
					}
				}

			} catch (\Exception $e) {
				Log::error(print_r($e, true), array(__CLASS__));
			}

			return 'success';

		}
		else {
			//验证失败
			return "fail";

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}


		//测试数据
		//http://duobaoweb/callback/alipay/callback?out_trade_no=G121823545294120&trade_no=2016011900042343551&notify_type=trade_status_sync&notify_id&sign_type=RSA&sign=jFKKlGDu0eeNmz70I0JCLngi4StyYjtBDgM4l52wHQJV51xMmn9ZfDQUt7veCmGxGCkZmln14yd6i52x/8ouPiesDj+xpC4oGAPKNBuCCqt7T7KOiCwG1AjFu69c1Bw79C&notify_time=2015-10-29 21:22:03&subject=【预售】苹果Apple iPhone 6s plus 128G&payment_type=1&trade_status=TRADE_SUCCESS&seller_id=2088911708976095&seller_email=zzl7690@163.com&buyer_id=2088102161654990&buyer_email=zzl7690@163.com&total_fee=50&quantity=50&price=1&body=recharge&gmt_create=2015-10-29 21:22:03&gmt_payment=2015-10-29 21:22:03&is_total_fee_adjust=N&use_coupon=N&discount=0.00&refund_status&gmt_refund=0000-00-00 00:00:00&redpacket_id=
	}
}
