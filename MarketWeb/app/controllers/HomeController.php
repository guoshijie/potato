<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use \Api\Server\UserServer;
use \Api\Server\AutoId;
use \App\Libraries\Curl;

class HomeController extends BaseController
{

    var $userServer;
    var $autoIdServer;

    public function __construct()
    {
        $this->userServer = new UserServer();
        $this->autoIdServer = new AutoId();
    }

    public function showWelcome()
    {
        return View::make('hello');
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
