<?php

namespace App\Http\Models\Product;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model{

	/*
	 * 获取商品列表
	 */
	public function getProductList($offset,$length){
		$data   =  DB::table('goods')
			->select('id','sh_category_id','goods_name','goods_num','shop_price',
				'suppliers_id','specs','goods_img')
			->where('is_down',0)
			->skip($offset)
			->take($length)
			->get();

		if(empty($data)){
			return false;
		}


		$suppliers_ids = array();
		$goods_ids = array();
		foreach($data as $goods_list){
			$goods_ids[] = $goods_list->id;
			$suppliers_ids[] = $goods_list->suppliers_id;
		}


		//分类
		$cateogrys  = $this->goodsCategory($goods_ids);

		//标签
		$tags      = $this->goodsTags($goods_ids);

		//供应商
		$suppliers  = $this->suppliers($suppliers_ids);



		foreach($data as $goods_info_list){

			$goods_info_list->product_tags = array();

			foreach($suppliers as $suppilers_list){
				if($goods_info_list->suppliers_id == $suppilers_list->id){
					$goods_info_list->suppilers_name = $suppilers_list->suppliers_name;
				}
			}

			foreach($cateogrys as $cateogry_list){
				if($goods_info_list->sh_category_id == $cateogry_list->id){
					$goods_info_list->category_name = $cateogry_list->cat_name;
				}
			}

			$goods_info_list->tag = isset($tags[$goods_info_list->id]) ? $tags[$goods_info_list->id] : [];

		}
		return $data;
	}


	/*
	 * 获取商品详情
	 */
	public function getProductById($good_id){
		$data   =  DB::table('goods')
			->select('id','sh_category_id','goods_name','provider_name','goods_num','shop_price',
				'suppliers_id','goods_desc','specs','model','goods_thumb','goods_img')
			->where('is_down',0)
			->where('id',$good_id)
			->first();

		if(empty($data)){
			return false;
		}

		//分类
		$cateogry = $this->goodsCategory(array($data->id));

		//标签
		$tag      = $this->goodsTags(array($data->id));

		//图片
		$pics     = $this->goodsPics(array($data->id));

		$data->cateogry = $cateogry[0]->cat_name;
		$data->tag      = $tag;
		$data->pics     = $pics;
		return $data;


	}



	/*
	 * 商品分类
	 */
	private function goodsCategory($goods_ids){
		return DB::table('goods_category')->whereIn('id',$goods_ids)->where('is_delete',0)->select('id','cat_name')->get();

	}

	/*
	 * 商品图片集合
	 */
	private function goodsPics($goods_ids){
		return DB::table('goods_pic')->select('pic_url')->whereIn('sh_goods_id',$goods_ids)->get();
	}

	/*
	 * 商品标签集合
	 */
	private function goodsTags($goods_ids){
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


	/*
	 * 获取供应商
	 */
	private function suppliers($suppliers_ids){
		return DB::table('suppliers')->whereIn('id',$suppliers_ids)->where('status',0)->select('id','suppliers_name')->get();
	}
}
