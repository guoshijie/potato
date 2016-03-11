<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Goods as GoodsServer;
use \Api\Server\Cart as CartServer;
use App\Http\Controllers\ApiController;
class GoodsController extends ApiController
{

	var $goodsServer;

	public function __construct()
	{
		$this->goodsServer = new GoodsServer();
	}


	public function index(){

		if(!Request::has('page')){
			return Response::json($this->response(10005));
		}

		$page    =   Request::get('page');

		$goodsList = $this->goodsServer->index($page);
		$goodsList = json_decode($goodsList);
		if(isset($goodsList->data->product_list)){
			foreach($goodsList->data->product_list as $v){
				$v->cart = 0;
			}
		}

		if( !$this->isLogin()){
			return Response::json($goodsList);
		}

		if($goodsList->code==0 || !$goodsList->data){
			return Response::json($goodsList);
		}

		foreach($goodsList->data->product_list as $v){
			$goodsIds[] = $v->id;
		}

		$cartServer = new CartServer();
		$cartList = $cartServer->getCartGoodsNum($this->loginUser->id, array('goods_ids'=>$goodsIds));
		$cartList = json_decode($cartList);
		if($cartList->code==0 || !$cartList->data){
			return Response::json($goodsList);
		}

		foreach($goodsList->data->product_list as $v){
			foreach($cartList->data as $vc){
				if($v->id==$vc->goods_id){
					$v->cart = $vc->goods_num;
				}else{
					$v->cart = 0;
				}
			}
		}

		return Response::json($goodsList);
	}


	public function detail(){

		if(!Request::has('goods_id')){
			return Response::json($this->response(10005));
		}

		$goods_id    =   Request::get('goods_id');

		$content = $this->goodsServer->detail($goods_id);
		return Response::json(json_decode($content));
	}



}
