<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use \Api\Server\UserServer;
use \Api\Server\AutoId;
use \Api\Server\AdvertServer\Banner;
use \App\Libraries\Curl;
use App\Http\Controllers\ApiController;

class HomeController extends ApiController
{

    var $userServer;
    var $autoIdServer;

    public function __construct()
    {
        parent::__construct();
        $this->autoIdServer = new AutoId();
    }

    public function showWelcome()
    {
        $server = new Banner();
		$id = $server->get(1);
      //$id = $this->autoIdServer->get(AutoId::TEST);
        return Response::json($this->response(1,''));
    }

    public function getUser()
    {
       $id = Input::get("id");
       $user = $this->userServer->get($id);
       if(!$user){
           return Response::json(array("code"=>"获取错误"));
       }
       return Response::json($user);
    }

    public function createUser()
    {
        $name = Input::get("name");
        $id = $this->autoIdServer->get(AutoId::TEST);
        $user = $this->userServer->create($id, $name);
        return Response::json($user);
    }
}
