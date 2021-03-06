<?php
/**
 * 用户信息控制器
 *
 * @author		liangfeng@shinc.net
 * @version		v1.0
 * @copyright	shinc
 */
namespace App\Http\Controllers\User;		// 定义命名空间
use App\Http\Controllers\ApiController;    //导入基类
use Illuminate\Http\Request;                //输入输出类
use App\Http\Models\User\ResetModel;			//引入model
use  App\Libraries\Qiniu\Auth;              //引入七牛
use  App\Libraries\Qiniu\Processing\PersistentFop;
use  App\Libraries\Qiniu\Storage\UploadManager;

class UserController extends ApiController
{

	public function __construct() {

	}

	/**
	 * 修改用户头像
	 *
	 * @param string $head_pic 用户头像
	 * @return json
	 */

	public function editHeadPic(Request $request) {
		if( !$request->has('head_pic') || !$request->has('user_id') ) {
			return $this->response(10005);
		}
		$user = new ResetModel();
		$head_pic = $request->get( 'head_pic' );
		$user_id  = $user->findByUserId($request->get('user_id'));

		if(!$user_id){
			return $this->response(10013);
		}
		$res = $user->updateHeadPic( $user_id->id, $head_pic );
		if( $res ) {
			return $this->response(1);
		}else{
			return $this->response(0);
		}

	}


	/*
	 * 七牛上传图片token
	 */
	public function uploadQiniuToken(Request $request){
		if(!$request->has('image')){
			return $this->response(0 , '请传入image 参数');
		}
		$image=$request->get('image');
		$accessKey = 'h591Hrv-oh3BornRVEQqlDE7IJQYFgM-dkA44tKM';
		$secretKey = 'XFwQNCCycfAf6fv_Ox-teKB8Tf2Bk21Xr5cqYXEm';
		$qiniu  = new Auth($accessKey , $secretKey);
		$data['token'] = $qiniu->uploadToken($image);

		return $this->response(1, '成功' ,$data);
	}

	/*
	 *  意见反馈
	 */
	public function setOpinion(Request $request){
		if(!$request->has('content')){
			return $this->response(10005);
		}

		if(!$request->has('user_id')){
			return $this->response(10013);
		}

		$user_id = $request->get('user_id');
		$content = $request->get('content');
		$user = new ResetModel();
		$res = $user->setOpinionByUserId( $user_id, $content );
		if( $res ) {
			return $this->response(1);
		}else{
			return $this->response(0);
		}
	}

}
