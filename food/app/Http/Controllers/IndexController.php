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

class IndexController extends ApiController
{

    var $userServer;
    var $autoIdServer;

    public function __construct()
    {
        parent::__construct();
        $this->autoIdServer = new AutoId();
    }

    /**
     * 显示列表.
     *
     * @return Response
     */
    public function index()
    {
        $server = new Banner();
		$data = $server->get(1);
        return $data;
    }

    /**
     * 创建新表单页面
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * 将新创建的存储到存储器
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * 显示指定
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * 显示编辑指定的表单页面
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 在存储器中更新指定
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 从存储器中移除指定
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
