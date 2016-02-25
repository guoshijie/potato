<?php
/*
 * 控制器用例
 */
namespace App\Http\Controllers\Demo;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Http\Response;           //响应类
use App\Http\Models\Demo\DemoModel;

class DemoController extends ApiController
{
	protected $_model;
	public function __construct(){
		$this->_model = new DemoModel();
	}

	public function index(){
		$data =  $this->_model->find();
		if($data){
			return $this->response('1','获取成功',$data);
		}else{
			return $this->response(0);
		}
	}
}