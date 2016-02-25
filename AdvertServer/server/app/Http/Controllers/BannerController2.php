<?php
/**
 * @author		wuhui@shinc.net
 * @version		v1.0
 * @copyright	shinc
 */

namespace Laravel\Controller\Admin;

use BaseController;

use Laravel\Model\Admin\BannerModel;
use AdminController;
//use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class BannerController extends AdminController {
	private $bannerModel;
	
	public function __construct(){
		parent::__construct();
		$this->bannerModel = new BannerModel();
	}

    public function getIndex(){
		$data = array();
		$data['selected'] = 'index';
		$data['banner_list'] = $this->bannerModel->getBannerList();
        return Response::view('admin.index.banner', $data);
	}
	
	public function getAdd() {
		$data = array();
		$data['selected'] = 'index';
		return Response::view('admin.index.banner_add', $data);
	}

	public function postAdd() {
		$data = Input::all();
		if (!$data) return Response::json(array('code' => '-1', 'msg' => 'invalid data'));
		if ($this->bannerModel->addBanner($data)) return Response::json(array('code' => '1', 'msg' => 'success'));
		else return Response::json(array('code' => '0', 'msg' => 'fail'));
	}
	
	public function getEdit() {
		$banner_id = Input::get('banner_id');
		$data = array();
		$data['selected'] = 'index';
		$data['banner'] = $this->bannerModel->getBannerById($banner_id);
		return Response::view('admin.index.banner_edit', $data);
	}
	
	public function postEdit() {
		$data = Input::all();
		if (!$data) return Response::json(array('code' => '-1', 'msg' => 'invalid data'));
		if (isset($data['banner_id']) && !empty($data['banner_id'])) {
			$banner_id = $data['banner_id'];
			unset($data['banner_id']);
		} else return Response::json(array('code' => '-2', 'msg' => 'invalid id'));
		if ($this->bannerModel->editBanner($banner_id, $data)) return Response::json(array('code' => '1', 'msg' => 'success'));
		else return Response::json(array('code' => '0', 'msg' => 'fail'));
	}
	
	public function postDel() {
		$banner_id = Input::get('banner_id');
		if (!$banner_id) return Response::json(array('code' => '-1', 'msg' => 'invalid banner id'));
		if ($this->bannerModel->delBanner($banner_id)) return Response::json(array('code' => '1', 'msg' => 'success'));
		else return Response::json(array('code' => '0', 'msg' => 'fail'));
	}

	/*
	 * 欢迎页
	 */
	public function anyWelcome(){
		$banner_id = Input::get('banner_id');
		$data = array();
		$data['selected'] = 'index';
		$data['banner'] = $this->bannerModel->getWelcome();
		return Response::view('admin.index.banner_welcome', $data);
	}


	/*
	 * 支付页图
	 */
	public function anyRecharge(){
		$banner_id = Input::get('banner_id');
		$data = array();
		$data['selected'] = 'index';
		$data['banner'] = $this->bannerModel->getRecharge();
		return Response::view('admin.index.banner_recharge', $data);
	}
}
