<?php

namespace App\Http\Models\Cart;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CartModel extends Model{


	/*
	 * 添加商品到我的购物车（单个）
	 *
	 */
	public function addGoodsToCart($user_id,$goods_id,$goods_num){

		//检测虚假商品

		$check_nums  =   DB::table('goods')->where('id',$goods_id)->select('id','goods_num')->first();

		if(empty($check_nums)){
			return array('code'=>20302,'msg'=>'检测到不存在商品，用户不良行为','data'=>$check_nums);
		}

		//检测库存数量
		if($check_nums->goods_num < $goods_num){
			return array('code'=>40001,'msg'=>'库存数量不足');
		}


		//限制购物车数量
		$limit_cart = DB::table('cart')->where('user_id',$user_id)->where('is_delete',0)->count();
		//pr($limit_cart);
		if($limit_cart > 30){
			return array('code'=>40003,'msg'=>'购物车已满30笔，请先删除多余订单','data'=>$limit_cart);
		}

		//相同累加
		$look_cart = DB::table('cart')->where('user_id',$user_id)->where('goods_id',$goods_id)->where('is_delete',0)->first();
		if($look_cart){
			$data = array(
				'user_id'   =>$user_id,
				'goods_id'  =>$goods_id,
				'goods_num' =>$look_cart->goods_num+$goods_num
			);
			$carts = DB::table('cart')->where('id', $look_cart->id)->update($data);
		}else{
			//插入购物车
			$data = array(
				'user_id'   =>$user_id,
				'goods_id'  =>$goods_id,
				'goods_num' =>$goods_num
			);
			$carts = DB::table('cart')->insertGetId($data);
		}

		if($carts){
			return array('code'=>1,'msg'=>'添加成功');
		}else{
			return array('code'=>0,'msg'=>'添加失败');
		}

	}




	/*
	 * 添加商品到我的购物车（多个）
	 *
	 */
	public function addGoodsToCarts($user_id,$goods){
		try{
			$infput_goods_ids = array();
			foreach($goods as $vg){
				$infput_goods_ids[] = $vg->goods_id;
			}

			if(empty($infput_goods_ids)){
				return false;
			}

		}catch (\Exception $e){
			Log::error($e);
			return false;
		}

		//检测虚假商品
		$check_nums  =   DB::table('goods')->whereIn('id',$infput_goods_ids)->select('id','goods_num')->where('is_down',0)->get();

		if(empty($check_nums)){
			return array('code'=>20302,'msg'=>'检测到不存在商品，用户不良行为','data'=>$check_nums);
		}

		//检测库存数量
		foreach($check_nums as $vgn){
			foreach($goods as $vg){
				if($vgn->goods_num < $vg->goods_num){
					return array('code'=>40001,'msg'=>'库存数量不足');
				}
			}
		}

		//限制购物车数量
		$limit_cart = DB::table('cart')->where('user_id',$user_id)->where('is_delete',0)->count();
		if($limit_cart > 30){
			return array('code'=>40003,'msg'=>'购物车已满30笔，请先删除多余订单','data'=>$limit_cart);
		}

/*
		$look_cart = DB::table('cart')->where('user_id',$user_id)->whereIn('goods_id',$infput_goods_ids)->where('is_delete',0)->get();
		//debug($look_cart);
		if(!empty($look_cart)){
			//相同累加
			$sql_one ='update sh_cart set goods_num = (case ';
			$sql_two = '';
			$lc_goods_ids = array();
			foreach($look_cart as $vlc){
				$lc_goods_ids[] = $vlc->goods_id;
				foreach($goods as $vg ){
					if($vlc->goods_id == $vg->goods_id){
						$num = $vlc->goods_num+$vg->goods_num;
						$sql_two .= 'when goods_id='.$vg->goods_id.' then '.$num.' ';
					}
				}
			}

			$keyInfos       = implode( ',', $lc_goods_ids ) ;
			$sql_three = 'else goods_num end) where goods_id IN ('.$keyInfos.')';
			//debug($sql_three);
			$carts = DB::update($sql_one.$sql_two.$sql_three);

			if(!$carts){
				return false;
			}


			//不同直压   $lc_goods_ids  $infput_goods_ids
			$diff = array_diff($infput_goods_ids,$lc_goods_ids);

			if(!empty($diff)){

				$data = array();
				foreach($goods as $vg){
					foreach($diff as $vd){
						if($vg->goods_id == $vd){
							$data[] = array(
								'user_id'   => $user_id,
								'goods_id'  => $vg->goods_id,
								'goods_num' => $vg->goods_num
							);
						}
					}
				}

				//debug($data);
				$carts = DB::table('cart')->insert($data);
			}

		}else{
 */
			//	删除旧的
			DB::table('cart')->where('user_id', $user_id)->delete();

			//写入新的
			foreach($goods as $vg){
				$data[] = array(
					'user_id'   => $user_id,
					'goods_id'  => $vg->goods_id,
					'goods_num' => $vg->goods_num,
					'is_select' => $vg->is_select
				);
			}
			DB::table('cart')->insert($data);

			return array('code'=>1,'msg'=>'添加成功');

	}


	/*
	 * 获取购物车列表
	 */
	public function getCartListByUserId($user_id, $is_select){
		if($is_select!==null){
			$carts = DB::table('cart')->where('user_id',$user_id)->where('is_select', $is_select)->where('is_delete',0)->get();
		}else{
			$carts = DB::table('cart')->where('user_id',$user_id)->where('is_delete',0)->get();
		}
		//pr($carts);
		if(empty($carts)){
			return array();
		}

		//debug($carts);

		$goods_ids = array();
		foreach($carts as $cart_list){
			$goods_ids[] = $cart_list->goods_id;
		}

		$goods_ids = array_unique($goods_ids);

		//获取商品信息
		$data   =  DB::table('goods')
			->select('id','sh_category_id','goods_name','goods_num','shop_price',
				'suppliers_id','specs','model','goods_img','goods_fee','unit')
			->where('is_down',0)
			->whereIn('id',$goods_ids)
			->get();

		if(empty($data)){
			return false;
		}


		$catgegory_ids  = array();
		$suppliers_ids = array();
		$goods_ids = array();
		foreach($data as $goods_list){
			$goods_ids[] = $goods_list->id;
			$catgegory[]     = $goods_list->sh_category_id;
			$suppliers_ids[] = $goods_list->suppliers_id;
		}


		//分类
		$cateogrys  = $this->goodsCategory($catgegory_ids);

		//标签
		$tags      = $this->goodsTags($goods_ids);


		//校验库存
		$suppliers = array();
		foreach($data as $vg){
			// 选中的购物车数量
			$vg->order_num    = '';
			if($vg->goods_num == 0 ){
				$vg->last_num = '库存数量不足';
			}
			foreach($carts as $cart){
				if($vg->id == $cart->goods_id){
					$vg->order_num    = $cart->goods_num;
					if($vg->goods_num <= $cart->goods_num){
						$vg->last_num = '库存数量不足';
					}else{
						$vg->last_num = '1';
					}
					$vg->is_select = $cart->is_select;
				}
			}

			$suppliers[$vg->suppliers_id][] = $vg;


			foreach($cateogrys as $cateogry_list){
				if($vg->sh_category_id == $cateogry_list->id){
					$vg->category_name = $cateogry_list->cat_name;
				}
			}

			$vg->tag = isset($tags[$vg->id]) ? $tags[$vg->id] : [];
		}

		$supplier_ids = array_unique($suppliers_ids);
		//供应商
		$supplier_list = $this->suppliers($supplier_ids);

		$supplly = array();
		foreach($supplier_list as $v){
			$v->goods_list = $suppliers[$v->id];
			$v->total_price    = 0; //选中的总价
			foreach($v->goods_list as $vvg){
				$vvg->suppliers_name = $v->suppliers_name;
				$v->total_price += $vvg->order_num*$vvg->shop_price;
			}
			$supplly['suppliers'][] = $v;
		}

		return $supplly;

	}


	/*
	 * 获取购物车数量
	 */
	public function getCartNumByUserId($user_id){
		return DB::table('cart')->select('user_id',$user_id)->where('is_delete',0)->count();
	}

	/*
	 * 获取购物车数量
	 */
	public function getCartGoodsNum($user_id, $goods_ids=array()){
		$table = DB::table('cart')->select('goods_id','goods_num','is_select')->where('user_id',$user_id)->where('is_delete',0);
		if(!empty($goods_ids)){
			$table->whereIn('goods_id', $goods_ids);
		}
		return	$table->get();
	}

	/*
	 * 商品分类
	 */
	public function goodsCategory($catgegory_ids){
		return DB::table('goods_category')->whereIn('id',$catgegory_ids)->where('is_delete',0)->select('id','cat_name')->get();

	}

	/*
	 * 商品图片集合
	 */
	public function goodsPics($goods_ids){
		return DB::table('goods_pic')->select('pic_url')->whereIn('sh_goods_id',$goods_ids)->get();
	}

	/*
	 * 商品标签集合
	 */
	public function goodsTags($goods_ids){
		$good_tags = DB::table('goods_tag')->whereIn('goods_id',$goods_ids)->get();

		$tags_ids   = array();
		foreach($good_tags as $vgt){
			$tags_ids[] = $vgt->tag_id;
		}

		$tags      = DB::table('tag')->select('tag_id','name')->whereIn('tag_id',$tags_ids)->where('is_delete',0)->get();

		$data = array();
		foreach($good_tags as $vg){
			foreach ($tags as $vt) {
				if($vg->tag_id == $vt->tag_id){
					$data[$vg->goods_id][] = $vt;
				}
			}

		}

		return $data;
	}


	public function suppliers($suppliers_ids){
		return DB::table('suppliers')->whereIn('id',$suppliers_ids)->where('status',0)->select('id','suppliers_name')->get();
	}

	public function clear($user_id){
		return DB::table('cart')->where('user_id', $user_id)->delete();
	}

	/*
	 * 删除购物车内容
	 */
	public function del($userIds, $goodsIds){
		if(!is_array($userIds) || !is_array($goodsIds)){
			return false;
		}
		return DB::table('cart')->whereIn('user_id', $userIds)->whereIn('goods_id', $goodsIds)->delete();
	}



}
