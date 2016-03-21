<?php
/* *
 * 功能：微信预支付操作
 * 版本：V1.0
 * 创建日期：2015-11-18
 * 作者：liangfeng@shinc.net
 * 说明：微信手机APP支付类型的预支付实现，与支付宝的业务逻辑不同，微信是需要在web服务器端实现预付款回传给APP端
 */
namespace App\Http\Controllers;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Http\Response;           //响应类
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Libraries\Curl;
use App\Http\Models\DemoModel;

class DemoController extends ApiController
{
	protected $_model;
	public function __construct(){
		$this->demoM = new DemoModel();
	}

	public function index(){
	}

	/*
	 * add
	 */
	public function add(Request $request){
		$messages = $this->vd([
			'name' => 'required',
			],$request, array(
				'name'=> '请填写名称'
			));
		if($messages!='') return $this->response(10005, $messages);
		$newData['name'] = $request->get('name');

		$this->demoM->add($newData);
	}

	/*
	 * edit
	 */
	public function edit(Request $request){
		$messages = $this->vd([
			'name' => 'required',
			],$request, array(
				'name'=> '请填写名称'
			));
		if($messages!='') return $this->response(10005, $messages);
		$upData['name'] = $request->get('name');

		$this->demoM->edit($upData);
	}

	/*
	 * list
	 */
	public function lists(Request $request){
		$messages = $this->vd([
			'category_id' => 'required',
			],$request);
		if($messages!='') return $this->response(10005, $messages);

		$where['category_id'] = $request->get('category_id');

		$pageinfo = $this->pageinfo($request);
		$list = $this->demoM->getList($pageinfo->offset, $pageinfo->length, $where);
		
		return $this->response(1,'成功',$list);
	}

	/*
	 * detail
	 */
	public function detail(Request $request){
		$messages = $this->vd([
			'id' => 'required',
			],$request);
		if($messages!='') return $this->response(10005, $messages);

		$id = $request->get('id');

		$detail = $this->demoM->getDetail($id);
		
		return $this->response(1,'成功',$detail);
	}

	/**
	 * @param     $request
	 * @param int $length
	 * @return \stdClass
	 */
	private function pageinfo($request,$length=20){
		$pageinfo               = new \stdClass;
		$pageinfo->length       = $request->has('length') ? $request->get('length') : $length;;
		$pageinfo->page         = $request->has('page') ? $request->get('page') : 1;
		$pageinfo->offset		= $pageinfo->page<=1 ? 0 : ($pageinfo->page-1) * $pageinfo->length;
		//$page->totalNum     = (int)Product::getInstance()->getPurchaseTotalNum();
		$pageinfo->totalNum     = 0;
		$pageinfo->totalPage    = ceil($pageinfo->totalNum/$pageinfo->length);

		return $pageinfo;
	}

	private function log($msg){
		Log::error("--- DemoController error ---\n".$msg);
	}
}
