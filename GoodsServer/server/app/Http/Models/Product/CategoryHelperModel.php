<?php

namespace App\Http\Models\Product;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CategoryHelperModel extends Model {

	public function __construct(){
	}

	/*
	 * 新增多级分类
	 *
	 * DB		表映射类
	 * pkField		主键id
	 */
	public static function addC($newData, $table, $pkField='id'){
		if(isset($newData['pid']) && $newData['pid']>0){
			$row = DB::table($table)->select('top_id','level','pid_path','is_end')->where($pkField, $newData['pid'])->first();
			$newData['level'] = $row->level+1;
			$newData['top_id'] = $row->top_id;
			$newData['pid_path'] = $row->pid_path !='' ? $row->pid_path.'-'.$newData['pid'] : $newData['pid'];
			if($row->is_end==1){
				$up_is_end = 1;
			}

		}else{
			$newData['level'] = 1;
			$newData['pid_path'] = '';
		}
		$newData['is_end'] = 1;
		$newData['create_time'] = date('Y-m-d H:i:s');

		$newId = DB::table($table)->insertGetId($newData); // 一定要返回新id
		if($newData['level']==1){
			DB::table($table)->where($pkField, $newId)->update(array('top_id'=>$newId));
		}

		if(isset($up_is_end)){
			DB::table($table)->where($pkField, $newData['pid'])->update(array('is_end'=>0));
		}

		return $newId;
	}



	/*
	 * 编辑多级分类
	 *
	 * DB		表映射类
	 * pkField		主键id
	 */
	public static function editC($newData,$table, $pkField='id'){
		$oldRow = DB::table($table)->where($pkField, $newData[$pkField])->first();
		// 父级菜单
		if(isset($newData['pid']) && $oldRow->pid!=$newData['pid']){
			if($newData['pid']==0){
				$newData['top_id']		= $newData[$pkField];
				$newData['level']		= 1;
				$newData['pid_path']	= '';
			}else{
				$newParentRow = DB::table($table)->select('top_id','level','pid_path')->where($pkField, $newData['pid'])->first();
				$newData['top_id']		= $newParentRow->top_id;
				$newData['level']		= $newParentRow->level + 1;
				$newData['pid_path']	= $newParentRow->pid_path !='' ? $newParentRow->pid_path.'-'.$newData['pid'] : $newData['pid'];
			}

		}

		$isUp = DB::table($table)->where($pkField, $newData[$pkField])->update($newData);
		if($isUp && isset($newData['pid']) &&  $oldRow->pid!=$newData['pid']){
			// 更新子类的pid_path
			if($oldRow->pid!=$newData['pid']){
				$oldPath = $oldRow->pid_path !='' ? $oldRow->pid_path.'-'.$oldRow->$pkField : $oldRow->$pkField;  // 顶级路径为空
				$childList1 = DB::table($table)->select($pkField,'level','top_id','pid','pid_path')->where('pid_path', $oldPath)->get();
				$childList2 = DB::table($table)->select($pkField,'level','top_id','pid','pid_path')->where('pid_path', 'LIKE', $oldPath.'-%')->get();

				$childList = array_merge($childList1, $childList2);
				if(!empty($childList)){
					$childData = array();
					foreach($childList as $v){
						$childData['level']		= $v->level + ($newData['level'] - $oldRow->level);
						$childData['top_id']	= $newData['top_id'];
						$childData['pid_path']	= $newData[$pkField] . substr($v->pid_path, strlen($oldPath));
						if($newData['pid_path']!=''){
							$childData['pid_path'] = $newData['pid_path'] .'-' . $childData['pid_path'];
						}

						DB::table($table)->where($pkField,$v->$pkField)->update($childData);
					}
				}
			}
		}
		return $isUp;
	}


	/*
	 * 删除分类
	 */
	public static function delC($id, $table, $pkField='id'){
		$row = DB::table($table)->select($pkField)->where('pid', $id)->first();
		if($row){ // 有子类存在，请先删除子类
			return 0;
		}
		$isD =  DB::table($table)->where($pkField, $id)->delete();
		return $isD;
	}


	/*
	 *
	 */
	public static function getDetail($id, $table, $pkField='id'){
		$row = DB::table($table)->select('*')->where($pkField, $id)->first();
		return $row;
	}


	/*
	 * 获取分类列表
	 * list			已查出的分类数据
	 * pkField		表的主键id
	 * isMulti	是否多级多维返回
	 *		isMulti=false : 一维数组, 常用于表格层级展示
	 *		isMulti=true : 多维数组,常用于多级菜单，要有前端配合
	 */
	public static function getList($list, $pkField='id', $isMulti=false){
		if(empty($list)){return array();}
		$tmp = array();
		foreach($list as $v){
			//返回多维数组
			if(isset($tmp[$v->$pkField])){
				$v->child = $tmp[$v->$pkField]->child;
				unset($tmp[$v->$pkField]);
			}

			$tmp[$v->pid]->child[] = $v;
		}
		$newList = array();
		if($isMulti){
			$newList = $tmp[0]->child;
		}else{
			self::getChildren($tmp[0]->child, $newList);
		}
		return $newList;
	}

	private static function getChildren($child, &$newList){
		foreach($child as $v){
			$newV = clone $v;
			unset($newV->child);
			$newList[] =  $newV;
			if(isset($v->child)){
				self::getChildren($v->child, $newList);
			}
		}
	}

	/*
	 * 创建分类表
	 *
		CREATE TABLE IF NOT EXISTS `aaa_category` (
			`category_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(32) NOT NULL,
			`status` tinyint(1) NOT NULL DEFAULT '0',
			`remark` varchar(255) NOT NULL,
			`sort` smallint(6) unsigned NOT NULL,
			`pid` smallint(6) unsigned NOT NULL,
			`pid_path` varchar(255) NOT NULL COMMENT '完整路径',
			`level` tinyint(1) unsigned NOT NULL,
			`is_end` tinyint(1) NOT NULL,
			`top_id` int(10) unsigned NOT NULL,
			`type` tinyint(1) NOT NULL DEFAULT '0',
			`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`update_time` int(10) unsigned NOT NULL,
			PRIMARY KEY (`category_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8

	 */
}
