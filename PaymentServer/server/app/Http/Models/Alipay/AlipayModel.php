<?php

namespace App\Http\Models\Alipay;

use Illuminate\Support\Facades\DB;
//use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Model;
use Illuminate\Support\Facades\Log;

class AlipayModel extends Model{

	protected $table = 'jnl_alipay';

	/*
	 * 查询一条记录 by id
	 */
	public function load($id)
	{
		return DB::table($this->table)->where('trade_no', $id)->first();
	}


	/*
	 * 更新
	 */
	public function update($id, $data)
	{
		return DB::table($this->table)->where('trade_no', $id)->update($data);
	}


	/*
	 * 更新流水
	 */
	public function payCallbackUpdateJnl($out_trade_no, $pay_amount, $payment_type){

		//根据$payment_type类型，查询订单订单信息，
		//  `payment_type` varchar(8) NOT NULL DEFAULT '' COMMENT '支付类型 1为大订单，2为子订单，3',
		if($payment_type == 1){
			return $this->updateOrder($out_trade_no,$pay_amount);
		}elseif($payment_type == 2){
			return $this->updateSubOrder($out_trade_no,$pay_amount);
		}else{
			Log::info('根据$payment_type类型，查询订单订单信息，payment_type类型错误');
			return false;
		}

	}


	/*
	 * 更新大订单处理逻辑
	 */
	private function updateOrder($out_trade_no,$pay_amount){
		$data = DB::table('order')->where('order_no',$out_trade_no)->where('is_delete',0)->where('status',0)->first();

		if(empty($data)){
			Log::info('大订单处理逻辑，order表没有该订单数据，当前回调订单ID为:'.$out_trade_no);
			return false;
		}

		//获取订单金额和支付金额比对
		if($data->fact_gathering != $pay_amount){
			Log::info('获取订单金额和支付金额比对错误,当前回调交易金额为:'.$pay_amount.' 订单交易金额为: '.$data->fact_gathering);
			return false;
		}

		//记录流水
		$trans = array(
			'jnl_no'            =>  $out_trade_no,
			'user_id'           =>  $data->user_id,
			'trans_code'        =>  'order_no',
			'jnl_status'        =>  1,
			'jnl_message'       =>  '订单支付成功',
			'pay_type'          =>  0,
			'recharge_channel'  =>  0,
			'amount'            =>  $pay_amount,
			'create_time'       =>  date('Y-m-d H:i:s'),
			'update_time'       =>  date('Y-m-d H:i:s')
		);

		//判断交易记录表是否有该条数据
		$is_trans = DB::table('jnl_trans')->where('jnl_no',$out_trade_no)->first();

		if($is_trans){
			Log::info('该笔交易已存在操作记录');
			return false;
		}

		$jnl_trans = DB::table('jnl_trans')->insert($trans);

		if(!$jnl_trans){
			Log::info('写入交易流水信息错误,需要写入的信息为:'.print_r($trans,TRUE));
			return false;
		}


		//更新订单
		DB::table('order')->where('order_no',$out_trade_no)->where('is_delete',0)->where('status',0)->update(array('pay_status'=>2, 'status'=>1));

		$update_order_suppliers = DB::table('order_suppliers')->where('order_no',$out_trade_no)->where('is_delete',0)->where('status',0)->update(array('pay_status'=>2,'status'=>1));

		if(!$update_order_suppliers){
			Log::info('更新订单信息错误，请查看更新order表和order_suppliers表信息,当前为大订单交易，订单号为:'.$out_trade_no."update_order_suppliers为:".$update_order_suppliers);
			return false;
		}

		return true;

	}


	/*
	 * 更新子订单处理逻辑
	 */
	private function updateSubOrder($out_trade_no,$pay_amount){
		$data = DB::table('order_suppliers')->where('sub_order_no',$out_trade_no)->where('is_delete',0)->where('status',0)->first();

		if(empty($data)){
			Log::info('子订单处理逻辑，order_suppliers表没有该子订单数据，当前回调子订单ID为:'.$out_trade_no);
			return false;
		}

		$order = DB::table('order')->where('order_no',$data->order_no)->where('is_delete',0)->first();

		if(empty($order)){
			Log::info('子订单中的大订单处理逻辑，order表没有该订单数据，当前回调子订单ID为:'.$out_trade_no);
			return false;
		}

		//获取订单金额和支付金额比对
		if($order->fact_gathering != $pay_amount){
			Log::info('获取订单金额和支付金额比对错误,当前回调交易金额为:'.$pay_amount.' 订单交易金额为: '.$order->fact_gathering);
			return false;
		}

		//记录流水
		$trans = array(
			'jnl_no'            =>  $out_trade_no,
			'user_id'           =>  $order->user_id,
			'trans_code'        =>  'order_no',
			'jnl_status'        =>  1,
			'jnl_message'       =>  '订单支付成功',
			'pay_type'          =>  0,
			'recharge_channel'  =>  0,
			'amount'            =>  $pay_amount,
			'create_time'       =>  date('Y-m-d H:i:s'),
			'update_time'       =>  date('Y-m-d H:i:s')
		);

		//判断交易记录表是否有该条数据
		$is_trans = DB::table('jnl_trans')->where('jnl_no',$out_trade_no)->first();

		if($is_trans){
			Log::info('该笔交易已存在操作记录');
			return false;
		}

		$jnl_trans = DB::table('jnl_trans')->insert($trans);

		if(!$jnl_trans){
			Log::info('写入交易流水信息错误,需要写入的信息为:'.print_r($trans,TRUE));
			return false;
		}

		//更新订单
		//DB::table('order')->where('order_no',$data->order_no)->where('is_delete',0)->update(array('pay_status'=>2));

		$update_order_suppliers = DB::table('order_suppliers')->where('sub_order_no',$out_trade_no)->where('is_delete',0)->where('status',0)->update(array('pay_status'=>2,'status'=>1));

		if(!$update_order_suppliers){
			Log::info('更新订单信息错误，请查看更新order表和order_suppliers表信息,当前为大订单交易，订单号为:'.$out_trade_no);
			return false;
		}

		return true;
	}

	/*
	 * 支付结果
	 */
	public function getResult($out_trade_no, $payment_type){
		if($payment_type == 1){
			$row = DB::table('order')->select('pay_status')->where('order_no',$out_trade_no)->first();
		}elseif($payment_type == 2){
			$row = DB::table('order_suppliers')->select('pay_status')->where('sub_order_no',$out_trade_no)->first();
		}
		return $row;
	}
}
