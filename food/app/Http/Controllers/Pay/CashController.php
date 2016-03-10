<?php
namespace App\Http\Controllers\Pay;

use Illuminate\Support\Facades\Request;          
use Illuminate\Support\Facades\Response;
use \Api\Server\Pay as PayServer;
use App\Http\Controllers\ApiController;
class CashController extends ApiController
{

	public function __construct()
	{
	}

	public function anyIndex(){
		$params = Request::all();
		$server = new PayServer();
		return $server->post('/cash', $params);
	}
}
