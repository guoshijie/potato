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
		$catgegory_ids  = array();
		$goods_ids  = array();
		foreach($data as $goods_list){
			$goods_ids[] = $goods_list->id;
			$catgegory_ids[]     = $goods_list->sh_category_id;
			$suppliers_ids[] = $goods_list->suppliers_id;
		}


		//分类
		$cateogrys  = $this->goodsCategory($catgegory_ids);

		//标签
		$tags      = $this->goodsTags($goods_ids);

		//供应商
		$suppliers  = $this->suppliers($suppliers_ids);


		foreach($data as $goods_info_list){

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
			->select('id','sh_category_id','goods_name','goods_num','shop_price',
				'suppliers_id','goods_desc','specs','model','goods_thumb','goods_img')
			->where('is_down',0)
			->where('id',$good_id)
			->first();

		if(empty($data)){
			return false;
		}

		//分类
		$cateogry = $this->goodsCategory(array($data->sh_category_id));

		//标签
		$tag      = $this->goodsTags(array($data->id));

		//图片
		$pics     = $this->goodsPics(array($data->id));

		//供应商
		$suppliers  = $this->suppliers(array($data->suppliers_id));

		$data->cateogry = isset($cateogry[0]) ? $cateogry[0]->cat_name : '';
		$data->suppilers_name     = isset($suppliers[0]) ? $suppliers[0]->suppliers_name : '';
		$data->tag      = $tag;
		$data->pics     = $pics;
		$data->goods_desc = '2015年12月，以“互联互通，共享共治，构建网络空间命运共同体”为主题的第二届世界互联网大会乌镇峰会在浙江乌镇召开，习近平总书记出席大会开幕式并做了主旨演讲,详细阐述了互联网发展的重大意义和深远影响，提出了推进全球互联网治理体系变革“四项原则”和构建网络空间命运共同体“五点主张”。“四项原则”，即“尊重网络主权、维护和平安全、促进开放合作、构建良好秩序”。“五个主张”，即“加快全球网络基础设施建设，促进互联互通；打造网上文化交流共享平台，促进交流互鉴；推动网络经济创新发展，促进共同繁荣；保障网络安全，促进有序发展；构建互联网治理体系，促进公平正义”。';
		//$data->goods_desc = $data->goods_desc;

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
