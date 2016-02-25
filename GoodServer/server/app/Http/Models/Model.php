<?php
namespace App\Http\Models;

use Illuminate\Support\Facades\DB;

class Model
{

    protected $table;
    


    // 查询一条记录 by id
    public function load($id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }
    
    // 查询一条记录
    public function find($where=array())
    {
    	return DB::table($this->table)->where($where)->first();
    }
    
    // 查询列表
    public function findAll($where=array())
    {
    	return DB::table($this->table)->where($where)->get();
    }
    
    
    // 目录字典
    public function dict()
    {
        return DB::table($this->table)->lists('name', 'id');
    }



    // 添加
    public function add($data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    // 更新
    public function update($id, $data)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    // 删除
    public function delete($id)
    {
        return DB::table($this->table)->where('id', '=', $id)->delete();
    }
}