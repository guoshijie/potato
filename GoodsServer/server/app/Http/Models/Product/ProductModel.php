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
			->select('id','sh_category_id','goods_name','provider_name','goods_num','shop_price',
				'suppliers_id','specs','model','goods_thumb','goods_img')
			->where('is_down',0)
			->skip($offset)
			->take($length)
			->get();

		if(empty($data)){
			return false;
		}

		//分类
		$datas = $this->goodCategory($data);

		//标签
		$list  = $this->goodTags($datas[0],$datas[1]);

		//最低库存数量（未写）


		return $list;
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
			->get();

		//分类
		$datas = $this->goodCategory($data);

		//标签
		$list  = $this->goodTags($datas[0],$datas[1]);

		//图片
		$pics = $this->goodPics($datas[1]);

		foreach($list as $good){
			$good->pic = $pics;
		}

		//最低库存数量（未写）

		return $list;


	}



	/*
	 * 商品分类
	 */
	private function goodCategory($data){
		$category   = DB::table('goods_category')->where('is_delete',0)->get();

		$goods_ids = array();
		foreach($data as $key=>$value){
			$goods_ids[]    = $value->id;
			$value->category = '';
			foreach($category as $cate_K=>$cate_V){
				if($value->sh_category_id == $cate_V->id){
					$value->category = $cate_V->cat_name;
				}
			}
		}

		return array($data,$goods_ids);
	}

	/*
	 * 商品图片集合
	 */
	private function goodPics($goods_ids){
		$good_pics = DB::table('goods_pic')->whereIn('sh_goods_id',$goods_ids)->get();

		return $good_pics;
	}

	/*
	 * 商品标签集合
	 */
	private function goodTags($data,$goods_ids){
		$good_tags = DB::table('goods_tag')->whereIn('goods_id',$goods_ids)->get();

		$tags_ids   = array();
		foreach($good_tags as $list){
			$tags_ids[] = $list->tag_id;
		}

		$tags      = DB::table('tag')->select('tag_id','name')->whereIn('tag_id',$tags_ids)->where('is_delete',0)->get();

		foreach($data as $goods){
			$goods->product_tag = array();
			foreach($good_tags as $good_tag){
				if($goods->id == $good_tag->goods_id) {    //绑定关系表
					foreach ($tags as $tag) {
						if ($good_tag->tag_id == $tag->tag_id) {
							$goods->product_tag[] = $tag;
						}
					}
				}
			}
		}

		return $data;
	}
}
