<?php
/***
 * 现金支付
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
use App\Http\Models\Alipay\AlipayModel;
use Illuminate\Support\Facades\DB;

class CashController extends  ApiController {

	public function  __construct() {
	}

	public function index(Request $request){
		$messages = $this->vd([
			'out_trade_no' => 'required',
			'pay_type' => 'required',
			],$request);
		if($messages!='') return $this->response(10005, $messages);
		if($request->get('pay_type')==1){
			DB::table('order')->where('order_no', $request->get('out_trade_no') )->where('pay_status',0)->update(array('pay_status'=>3));
			$up = DB::table('order_suppliers')->where('order_no', $request->get('out_trade_no') )->where('pay_status',0)->update(array('pay_status'=>3));
		}else{
			$up = DB::table('order_suppliers')->where('son_order_no', $request->get('out_trade_no') )->where('pay_status',0)->update(array('pay_status=>3'));
		}
		if($up){
			return $this->response(1, '设为货到付款成功');
		}
		return $this->response(0, '未能改变货到付款状态，请查看是否已在线支付');
	}

}
