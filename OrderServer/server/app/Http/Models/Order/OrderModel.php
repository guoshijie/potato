<?php
/*
 * 订单服务
 * author:liangfeng@shinc.net
 */
namespace App\Http\Models\Order;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Http\Models\Cart\CartModel;

class OrderModel extends Model{

	/*
	 * 订单确认(下单)
	 * param $user_id   string  用户ID
	 * param $goods     array   商品参数(goods_id,goods_num)
	 * param $inv_payee string  发票抬头
	 */
	public function orderConfirmByUser($user_id,$goods,$inv_payee){

		//address

		$address = $this->getAdddressByUserId($user_id);

		if(empty($address)){
			return '-1';
		}

		try{

			$goods_ids = array();
			foreach($goods as $goods_list){
				$goods_ids[]    =   $goods_list->goods_id;
			}

			if(empty($goods_ids)){
				return '-2';
			}

		}catch (\Exception $e){
			Log::error($e);
			return '-3';
		}

		//cart
		$checkGoods = count($goods);
		$carts = DB::table('cart')->whereIn('goods_id',$goods_ids)->where('is_delete',0)->where('user_id',$user_id)->count();
		if($checkGoods != $carts){
			return '-4';
		}

		//goods
		$goods_info =   DB::table('goods')
			->whereIn('id',$goods_ids)
			->where('is_down',0)
			->select('id','sh_category_id','goods_name','goods_num','shop_price',
				'suppliers_id','specs','model','goods_img','goods_fee','unit')
			->orderBy('suppliers_id')
			->get();

		//tatol_price  and check stock
		$suppliers_ids = array();
		$tatol_price = '';
		foreach($goods_info as $goods_value){
			foreach($goods as $input_goods){
				if($goods_value->id == $input_goods->goods_id){

					//suppliers
					$suppliers_ids[] = $goods_value->suppliers_id;
					//tatol_price
					$tatol_price += $goods_value->shop_price * $input_goods->goods_num;
					//stock
					if($goods_value->goods_num < $input_goods->goods_num){
						return '-6';
					}
				}
			}
		}

		//suppliers
		$suppliers_ids = array_unique($suppliers_ids);

		//debug($suppliers_ids);
		//order_no
		$order_no = $this->generateJnlNo();


		//insert data
		$data = array(
			'order_no'      => $order_no,
			'user_id'       => $user_id,
			'name'          => $address->consignee,
			'phone'         => $address->tel,
			'address'       => $address->address,
			'district'      => $address->district,
			'store_name'    => $address->store_name,
			'total_price'   => $tatol_price,
			'fact_gathering'=> $tatol_price,
			'discount'      => 0,
			'shipping_fee'  => 0,
			'inv_payee'     => $inv_payee,
			'finish_time'   => date('Y-m-d H:i:s',strtotime('+1 week'))
		);

		$order_id = DB::table('order')->insert($data);
		if(!$order_id){
			return '-5';
		}


		$data_supplisers = array();
		foreach($suppliers_ids as $suppliers_id_list) {
			$tmp_sub_order_no = $this->generateJnlSonNo();
			$data_supplisers[] = array(
				'sub_order_no' => $tmp_sub_order_no,
				'order_no'     => $order_no,
				'suppliers_id' => $suppliers_id_list,
				'status'       => 0,
				'pay_status'   => 0,
				'is_delete'    => 0
			);
			$arr_sub_order_no[$suppliers_id_list] = $tmp_sub_order_no;
		}

		//debug($data_supplisers);
		DB::table('order_suppliers')->insert($data_supplisers);

		$data_info = array();
		foreach($goods_info as $goods_value){
			foreach($goods as $input_goods){
				if($goods_value->id == $input_goods->goods_id){
					//debug($order_no);
					$data_info[]=array(
						'order_no'      =>  $order_no,
						'sub_order_no'  =>  $arr_sub_order_no[$goods_value->suppliers_id],
						'goods_id'      =>  $goods_value->id,
						'goods_name'    =>  $goods_value->goods_name,
						'goods_pic'     =>  $goods_value->goods_img,
						'goods_num'     =>  $input_goods->goods_num,
						'price'         =>  $goods_value->shop_price,
						'price_total'   =>  $goods_value->shop_price * $input_goods->goods_num,
						'suppliers_id'  =>  $goods_value->suppliers_id,
						'category_id'   =>  $goods_value->sh_category_id,
						'specs'         =>  $goods_value->specs,
						'unit'          =>  $goods_value->unit
					);
				}
			}
		}


		//debug($data_supplisers);
		$order_info_id = DB::table('order_info')->insert($data_info);

		//insert order_info table to false,rollback order table.
		if(!$order_info_id){
			 DB::table('order')->where('id',$order_id)->update(array('is_delete',1));
			return '-6';
		}


		/*****************seccess all*********************/


		//delete cart table with it
		//DB::table('cart')->whereIn('goods_id',$goods_ids)->where('user_id',$user_id)->update(array('is_delete',1));
		$sql_1 ='update sh_cart set is_delete = (case ';
		$sql_2 = '';
		$goods_id = array();
		foreach($goods as $goods_value ){
			$goods_id[]=$goods_value->goods_id;
			$sql_2 .= 'when goods_id='.$goods_value->goods_id.' then '. '1 ';
		}
		$keyInfos       = implode( ',', $goods_id ) ;
		$sql_3 = 'else is_delete end) where goods_id IN ('.$keyInfos.')';
		$result = DB::update($sql_1.$sql_2.$sql_3);
		if(!$result){
			return '-7';
		}

		//update sh_cart set is_delete = (case when goods_id=146 then 1 when goods_id=147 then 1 when goods_id=150 then 1 else is_delete end) where goods_id IN (146,147,150)
		//reduce goods table for goods_num
		$sql_one ='update sh_goods set goods_num = (case ';
		$sql_two = '';
		foreach($goods_info as $goods_value){
			foreach($goods as $goods_val ){
				if($goods_value->id == $goods_val->goods_id){
					$num = $goods_value->goods_num - $goods_val->goods_num;
					$sql_two .= 'when id='.$goods_val->goods_id.' then '.$num.' ';
				}
			}
		}
		$sql_three = 'else goods_num end) where id IN ('.$keyInfos.')';
		$res = DB::update($sql_one.$sql_two.$sql_three);
		if(!$res){
			return '-7';
		}

		return $data;

	}


	/*
	 * 获取支付订单列表
	 * param $status   int	   状态
	 * param $user_id  string  用户ID
	 * param $offset   string  分页开始位置
	 * param $length   string  分页显示长度
	 * 逻辑:拿订单表->$order_no->拿子订单表信息->商品
	 */
	public function getOrderListByStatus($user_id, $offset, $length, $status){

		$orders = DB::table('order')
			->where('user_id',$user_id)
			->where('status',$status)
			->where('is_delete',0)
			->skip($offset)
			->take($length)
			->get();

		if(empty($orders)){
			return array();
		}

		$order_no = array();
		foreach($orders as $orders_list){
			$order_no[] =$orders_list->order_no;
		}

		//子订单
		$order_suppliers = DB::table('order_suppliers')
			->select('sub_order_no','order_no','suppliers_id','status','pay_status','create_time')
			->whereIn('order_no',$order_no)
			->where('is_delete',0)
			->where('status',$status)
			->get();

		if(empty($order_suppliers)){
			return array();
		}
		$data = $this->getOrderList($order_no,$order_suppliers);

		return $data;
	}


	/*
	 * 获取未支付订单列表
	 * param $user_id  string  用户ID
	 * param $offset   string  分页开始位置
	 * param $length   string  分页显示长度
	 * 逻辑:拿订单表->$order_no->拿子订单表信息->商品
	 */
	public function getOrderListByNoPay($user_id,$offset, $length){

		$orders = DB::table('order')
			->where('user_id',$user_id)
			->where('pay_status',0)
			->where('is_delete',0)
			->skip($offset)
			->take($length)
			->get();

		if(empty($orders)){
			return false;
		}

		$order_no = array();
		foreach($orders as $orders_list){
			$order_no[] =$orders_list->order_no;
		}

		//子订单
		$order_suppliers = DB::table('order_suppliers')
			->select('sub_order_no','order_no','suppliers_id','status','pay_status','create_time')
			->whereIn('order_no',$order_no)
			->where('is_delete',0)
			->where('pay_status',0)
			->where('status',0)
			->get();

		$data = $this->getOrderList($order_no,$order_suppliers);

		return $data;
	}


	/*
	 * 获取待收货订单列表
	 * param $user_id  string  用户ID
	 * param $offset   string  分页开始位置
	 * param $length   string  分页显示长度
	 * 逻辑:拿订单表->$order_no->拿子订单表信息->商品
	 */
	public function getOrderListByWaiting($user_id,$offset, $length){

		$orders = DB::table('order')
			->where('user_id',$user_id)
			->where('pay_status',2)
			->where('is_delete',0)
			->skip($offset)
			->take($length)
			->get();

		if(empty($orders)){
			return false;
		}

		$order_no = array();
		foreach($orders as $orders_list){
			$order_no[] =$orders_list->order_no;
		}

		//子订单
		$order_suppliers = DB::table('order_suppliers')
			->select('sub_order_no','order_no','suppliers_id','status','pay_status','create_time')
			->whereIn('order_no',$order_no)
			->whereIn('status',array(3,4))
			->where('is_delete',0)
			->where('pay_status',2)
			->get();

		$data = $this->getOrderList($order_no,$order_suppliers);

		return $data;

	}


	/*
	 * 获取已完成订单列表
	 * param $user_id  string  用户ID
	 * param $satus    string  订单状态(1=未支付,2=待收货，3=已完成,4=已撤销)
	 * param $offset   string  分页开始位置
	 * param $length   string  分页显示长度
	 * 逻辑:拿订单表->$order_no->拿子订单表信息->商品
	 */
	public function getOrderListByFinish($user_id,$offset, $length){

		$orders = DB::table('order')
			->where('user_id',$user_id)
			->where('pay_status',2)
			->where('is_delete',0)
			->skip($offset)
			->take($length)
			->get();

		if(empty($orders)){
			return false;
		}

		$order_no = array();
		foreach($orders as $orders_list){
			$order_no[] =$orders_list->order_no;
		}

		//子订单
		$order_suppliers = DB::table('order_suppliers')
			->select('sub_order_no','order_no','suppliers_id','status','pay_status','create_time')
			->whereIn('order_no',$order_no)
			->where('is_delete',0)
			->where('pay_status',2)
			->where('status',5)
			->get();

		$data = $this->getOrderList($order_no,$order_suppliers);

		return $data;

	}


	/*
	 * 获取已撤销订单列表
	 * param $user_id  string  用户ID
	 * param $satus    string  订单状态(1=未支付,2=待收货，3=已完成,4=已撤销)
	 * param $offset   string  分页开始位置
	 * param $length   string  分页显示长度
	 */
	public function getOrderListByCancel($user_id,$offset, $length){
		$orders = DB::table('order')
			->where('user_id',$user_id)
			->where('status',2)
			->where('is_delete',0)
			->skip($offset)
			->take($length)
			->get();

		if(empty($orders)){
			return false;
		}

		$order_no = array();
		foreach($orders as $orders_list){
			$order_no[] =$orders_list->order_no;
		}

		//子订单
		$order_suppliers = DB::table('order_suppliers')
			->select('sub_order_no','order_no','suppliers_id','status','pay_status','create_time')
			->whereIn('order_no',$order_no)
			->where('is_delete',0)
			->where('status',2)
			->get();

		$data = $this->getOrderList($order_no,$order_suppliers);

		return $data;
	}


	/*
	 * 获取订单列表公共模块，四维数组压二维，好的代码应该不是循环嵌套
	 */
	private function getOrderList($order_no,$order_suppliers){
		foreach($order_suppliers as $v){
			$sub_order_noList[] = $v->sub_order_no;
		}

		//获取订单商品
		$order_info  = DB::table('order_info')->whereIn('sub_order_no',$sub_order_noList)->where('is_delete',0)->get();

		if(empty($order_info)){
			return array();
		}

		$category_ids  = array();
		$supplier_ids  = array();
		$goods_ids    = array();
		foreach($order_info as $order_info_list){
			$category_ids[$order_info_list->category_id]   = $order_info_list->category_id;
			$supplier_ids[$order_info_list->suppliers_id]   = $order_info_list->suppliers_id;
			$goods_ids[]      = $order_info_list->goods_id;
		}

		$cart_M = new CartModel();

		$cateogrys = $cart_M->goodsCategory($category_ids);

		$tags      = $cart_M->goodsTags($goods_ids);

		$suppliers = $cart_M->suppliers($supplier_ids);

		$tmp = array();
		foreach($order_info as $goods_info_list) {
			//分类
			foreach($cateogrys as $cateogry_list){
				if($goods_info_list->category_id == $cateogry_list->id){
					$goods_info_list->category_name = $cateogry_list->cat_name;
				}
			}

			//标签
			$goods_info_list->tag = isset($tags[$goods_info_list->id]) ? $tags[$goods_info_list->id] : [];

			//$tmp[$goods_info_list->order_no][$goods_info_list->suppliers_id][] = $goods_info_list;

			$tmp[$goods_info_list->sub_order_no][] = $goods_info_list;

		}

		$data = array();
		foreach($order_suppliers as $vs){

			//供应商
			foreach($suppliers as $suppilers_list){
				if($vs->suppliers_id == $suppilers_list->id){
					$vs->suppilers_name = $suppilers_list->suppliers_name;
				}
			}

			$vs->order_info = isset($tmp[$vs->sub_order_no]) ? $tmp[$vs->sub_order_no] : array();
			$data[$vs->sub_order_no] = $vs;
		}

		return array_values($data);

		//debug($data);
	}



	/*
	 * 根据订单ID获取订单详细信息
	 * param    $user_id    string  用户ID
	 * param    $order_id   string  订单ID
	 */
	public function getOrderDetailByOrderId($user_id,$order_no,$son_order_no){
		$data   = DB::table('order')->select('order_no','name','phone','address','store_name','district','pay_type','inv_payee','create_time','finish_time','end_time')->where('user_id',$user_id)->where('order_no',$order_no)->where('is_delete',0)->first();

		if(empty($data)){
			return false;
		}

		//子订单
		$order_suppliers = DB::table('order_suppliers')->where('sub_order_no',$son_order_no)->where('is_delete',0)->first();

		//$data->detail_order = $order_suppliers;

		//商品信息
		$order_info  = DB::table('order_info')->where('order_no',$order_no)->where('suppliers_id',$order_suppliers->suppliers_id)->where('is_delete',0)->get();


		//商品分类、标签、供应商信息
		$category_ids  = array();
		$supplier_ids  = array();
		$goods_ids    = array();
		foreach($order_info as $order_info_list){
			$category_ids[]   = $order_info_list->category_id;
			$supplier_ids[]   = $order_info_list->suppliers_id;
			$goods_ids[]      = $order_info_list->goods_id;
		}

		$cart_M = new CartModel();

		$cateogrys = $cart_M->goodsCategory($category_ids);

		$tags      = $cart_M->goodsTags($goods_ids);

		$suppliers = $cart_M->suppliers($supplier_ids);

		foreach($order_info as $goods_info_list) {

			//分类
			foreach($cateogrys as $cateogry_list){
				if($goods_info_list->category_id == $cateogry_list->id){
					$goods_info_list->category_name = $cateogry_list->cat_name;
				}
			}

			//标签
			$goods_info_list->tag = isset($tags[$goods_info_list->id]) ? $tags[$goods_info_list->id] : [];

			//供应商
			foreach($suppliers as $suppilers_list){
				if($goods_info_list->suppliers_id == $suppilers_list->id){
					$goods_info_list->suppilers_name = $suppilers_list->suppliers_name;
					$order_suppliers->suppilers_name = $suppilers_list->suppliers_name;
				}
			}
		}

		$order_suppliers->product_list = $order_info;
		$data->detail_order = $order_suppliers;

		return $data;

	}


	/*
	 * 取消大订单
	 */
	public function cancelOrderNo($user_id,$order_no){
		//更新订单表
		$up = DB::table('order')->where('order_no',$order_no)->where('is_delete',0)->where('status', 0)
			->update(array('status'=>3));
		if(!$up){
			return 0;
		}

		//更新子订单表
		$up = DB::table('order_suppliers')->where('order_no',$order_no)->where('is_delete',0)->where('status', 0)
			->update(array('status'=>3));
		if(!$up){
			return 0;
		}

		/****************************恢复商品数量************************/
		$order_info = DB::table('order_info')->select('goods_id','goods_num')->where('is_delete',0)->where('order_no',$order_no)->get();

		$goods_ids = array();
		foreach($order_info as $voi){
			$goods_ids[] = $voi->goods_id;
		}

		$goods = DB::table('goods')->select('id','goods_num')->whereIn('id',$goods_ids)->where('is_down',0)->get();

		$sql_one ='update sh_goods set goods_num = (case ';
		$sql_two = '';
		foreach($goods as $goods_value){
			foreach($order_info as $goods_val ){
				if($goods_value->id == $goods_val->goods_id){
					$num = $goods_value->goods_num + $goods_val->goods_num;
					$sql_two .= 'when id='.$goods_val->goods_id.' then '.$num.' ';
				}
			}
		}

		$keyInfos       = implode( ',', $goods_ids ) ;
		$sql_three = 'else goods_num end) where id IN ('.$keyInfos.')';
		//debug($sql_three);
		$res = DB::update($sql_one.$sql_two.$sql_three);


		if(!$res){
			return false;
		}

		return $res;

	}


	/*
	 * 取消子订单
	 * 先取子订单表
	 */
	public function cancelSubOrderNo($user_id,$sub_order_no){
/*
		//debug($sub_order_no);
		//得到子订单号
		$order_suppliers = DB::table('order_suppliers')->where('sub_order_no',$sub_order_no)->where('is_delete',0)->first();
		if(empty($order_suppliers)){
			return false;
		}

		$order_no = $order_suppliers->order_no;

		//判断订单是否存在
		$data = DB::table('order')->select('order_no')->where('order_no',$order_no)->where('status','!=',2)->where('user_id',$user_id)->where('is_delete',0)->first();

		if(empty($data)){
			return false;
		}
		//判定订单对应关系(单笔订单，同步取消大订单)
		$sub_order_count = DB::table('order_suppliers')->where('order_no',$order_no)->where('is_delete',0)->count();

		//debug($sub_order_count);
		if($sub_order_count == 1){
			//更新订单表
			DB::table('order')->where('order_no',$order_no)->where('is_delete',0)->update(array('status'=>2));
		}
 */
		//更新子订单表
		$up = DB::table('order_suppliers')->where('sub_order_no',$sub_order_no)->where('is_delete',0)->where('status',0)
			->update(array('status'=>3));
		if(!$up){
			return 0;
		}

		/****************************恢复商品数量************************/
		$order_info = DB::table('order_info')->select('goods_id','goods_num')->where('is_delete',0)->where('order_no',$order_suppliers->order_no)->where('suppliers_id',$order_suppliers->suppliers_id)->get();

		$goods_ids = array();
		foreach($order_info as $voi){
			$goods_ids[] = $voi->goods_id;
		}

		$goods = DB::table('goods')->select('id','goods_num')->whereIn('id',$goods_ids)->where('is_down',0)->get();

		$sql_one ='update sh_goods set goods_num = (case ';
		$sql_two = '';
		foreach($goods as $goods_value){
			foreach($order_info as $goods_val ){
				if($goods_value->id == $goods_val->goods_id){
					$num = $goods_value->goods_num + $goods_val->goods_num;
					$sql_two .= 'when id='.$goods_val->goods_id.' then '.$num.' ';
				}
			}
		}

		$keyInfos       = implode( ',', $goods_ids ) ;
		$sql_three = 'else goods_num end) where id IN ('.$keyInfos.')';
		//debug($sql_three);
		$res = DB::update($sql_one.$sql_two.$sql_three);


		if(!$res){
			return false;
		}

		return $res;
	}


	/*
	 * 确认收货
	 * 先取子订单表
	 */
	public function confirmReceivingOrder($user_id,$sub_order_no){
		//得到子订单号
		$order_suppliers = DB::table('order_suppliers')->where('sub_order_no',$sub_order_no)->where('is_delete',0)->where('pay_status',2)->first();
		//debug($order_suppliers);

		if(empty($order_suppliers)){
			return false;
		}

		$order_no = $order_suppliers->order_no;

		//判断订单是否存在
		$data = DB::table('order')->select('order_no')->where('order_no',$order_no)->where('status','!=',1)->where('user_id',$user_id)->where('is_delete',0)->first();

		if(empty($data)){
			return false;
		}

		//判定订单对应关系(单笔订单，同步大订单)
		$sub_order_count = DB::table('order_suppliers')->where('order_no',$order_no)->where('is_delete',0)->count();

		//debug($sub_order_count);
		if($sub_order_count == 1){
			//更新订单表
			DB::table('order')->where('order_no',$order_no)->where('is_delete',0)->update(array('status'=>1));
		}

		//更新子订单表
		DB::table('order_suppliers')->where('sub_order_no',$sub_order_no)->where('is_delete',0)->update(array('status'=>1));

		return 1;

	}


	/*
	 * 联系卖家
	 */
	public function getSuppliersInformation($suppliers_id){

		return DB::table('suppliers')->where('id',$suppliers_id)->where('status',0)->first();
	}


	/**
	 * @param $user_id
	 * @param $type
	 * @return bool
	 */
	public function getOrderNumByUserId($user_id,$type=0){
		if($type == 1){ //待支付
			return DB::table('order')->where('user_id',$user_id)->where('is_delete',0)->where('pay_status','!=',2)->count();
		}elseif($type ==2){ //待收货
			return DB::table('order')->where('user_id',$user_id)->where('is_delete',0)->where('pay_status',2)->where('status',0)->count();
		}elseif($type ==3){ //已完成
			return DB::table('order')->where('user_id',$user_id)->where('is_delete',0)->where('pay_status',2)->where('status',5)->count();
		}else{ // all
			$num['unpaid'] = DB::table('order')->where('user_id',$user_id)->where('is_delete',0)->where('pay_status','!=',2)->count();
			$num['shipping'] = DB::table('order')->where('user_id',$user_id)->where('is_delete',0)->where('pay_status',2)->where('status',0)->count();
			$num['finished'] = DB::table('order')->where('user_id',$user_id)->where('is_delete',0)->where('pay_status',2)->where('status',5)->count();
			return $num;
		}
	}

	/*
	 * 根据用户ID获取默认收货地址
	 * param $user_id  string  用户ID
	 */
	public function getAdddressByUserId($user_id){
		return DB::table('user_address')->where('user_id',$user_id)->where('is_default',1)->first();
	}


	/**
	 * 生成唯一订单号
	 */
	function generateJnlNo() {
		date_default_timezone_set('PRC');
		$yCode = array('A','B','C','D','E','F','G','H','I','J');
		$orderSn = '';
		$orderSn .= $yCode[(intval(date('Y')) - 1970) % 10];
		$orderSn .= strtoupper(dechex(date('m')));
		$orderSn .= date('d').substr(time(), -5);
		$orderSn .= substr(microtime(), 2, 5);
		$orderSn .= sprintf('%02d', mt_rand(0, 99));
		return $orderSn;
	}


	/**
	 * 生成唯一子订单号
	 */
	function generateJnlSonNo() {
		date_default_timezone_set('PRC');
		$yCode = array('K','L','M','N','O','P','Q','R','S','T');
		$orderSn = '';
		$orderSn .= $yCode[(intval(date('Y')) - 1970) % 10];
		$orderSn .= strtoupper(dechex(date('m')));
		$orderSn .= date('d').substr(time(), -5);
		$orderSn .= substr(microtime(), 2, 5);
		$orderSn .= sprintf('%02d', mt_rand(0, 99));
		return $orderSn;
	}


}
