<?php

namespace App\Http\Models\Cart;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CartModel extends Model{


	/*
	 * 添加商品到我的购物车
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
	 * 获取购物车列表
	 */
	public function getCartListByUserId($user_id){

		$carts = DB::table('cart')->where('user_id',$user_id)->where('is_delete',0)->get();
		//pr($carts);
		if(empty($carts)){
			return false;
		}

		//debug($carts);

		$goods_ids = array();
		foreach($carts as $cart_list){
			$goods_ids[] = $cart_list->goods_id;
		}

		$goods_ids = array_unique($goods_ids);

		//获取商品信息
		$data   =  DB::table('goods')
			->select('id','sh_category_id','goods_name','provider_name','goods_num','shop_price',
				'suppliers_id','specs','model','goods_thumb','goods_img','goods_fee')
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
			$vg->order_num    = '';
			if($vg->goods_num == 0 ){
				$vg->last_num = '库存数量不足(对接时调为数字)';
			}
			foreach($carts as $cart){
				if($vg->id == $cart->goods_id){
					$vg->order_num    = $cart->goods_num;
					if($vg->goods_num <= $cart->goods_num){
						$vg->last_num = '库存数量不足(对接时调为数字)';
					}else{
						$vg->last_num = '1';
					}
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
			$supplly['suppliers'][] = $v;
		}

		return $supplly;

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
}
