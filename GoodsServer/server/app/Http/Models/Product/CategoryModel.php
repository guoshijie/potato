<?php

namespace App\Http\Models\Product;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Product\CategoryHelperModel AS CategoryHM;;


class CategoryModel extends Model{
	/*
	 * 新增商品分类
	 */
	public function addGoodsCategory($data){
		$newId = CategoryHM::addC($data, 'goods_category');
		return $newId;
	}

	/*
	 * 编辑商品分类
	 */
	public function editGoodsCategory($data){
		return CategoryHM::editC($data, 'goods_category');
	}

	public function delGoodsCategory($id){
		$isD = CategoryHM::delC($id, 'goods_category');
		if($isD===0){ // 有子类存在，请先删除子类;
		}
		return $isD;
	}

}
