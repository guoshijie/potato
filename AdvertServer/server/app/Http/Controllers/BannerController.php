<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends ApiController
{

		
    /**
     * 显示列表.
     *
     * @return Response
     */
    public function index(Request $request)
    {
		$table = DB::table('banner');
		if($request->has('type')){
			$list = $table->select('pic_url')->where('type', $request->get('type'))->get();
			if($request->get('type')==2){
				$list = $list[0];
			}
		}
		$response = $this->response(1, '成功' ,$list);
		return json_encode($response);
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

