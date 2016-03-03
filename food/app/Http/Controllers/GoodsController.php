<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Goods as GoodsServer;
use App\Http\Controllers\ApiController;
class GoodsController extends ApiController
{

	var $goodsServer;

	public function __construct()
	{
		parent::__construct();
		$this->goodsServer = new GoodsServer();
	}


	public function index(){

		if(!Request::has('page')){
			return Response::json($this->response(10005));
		}

		$page    =   Request::get('page');

		return $this->goodsServer->index($page);

	}


	public function detail(){

		if(!Request::has('goods_id')){
			return Response::json($this->response(10005));
		}

		$goods_id    =   Request::get('goods_id');

		return Response::json($this->goodsServer->detail($goods_id));

	}



}
