<?php
namespace App\Libraries\Taobao;
/* *
* 类名：阿里大鱼短信平台
* 功能：给手机发送短信信息
* 详细：验证码等
*/

require dirname(__FILE__).'/TopSdk.php';
//date_default_timezone_set('Asia/Shanghai'); 
use App\Libraries\Taobao\Top\TopClient;
use App\Libraries\Taobao\Top\Request\AlibabaAliqinFcSmsNumSendRequest;
use App\Libraries\Taobao\Top\Request\AlibabaAliqinFcTtsNumSinglecallRequest;
use App\Libraries\Taobao\Top\Request\AlibabaAliqinFcVoiceNumDoublecallRequest;
use App\Libraries\Taobao\Top\Request\AlibabaAliqinFcVoiceNumSinglecallRequest;

require dirname(__FILE__).'/top/TopClient.php';
require dirname(__FILE__).'/top/request/AlibabaAliqinFcSmsNumSendRequest.php';
require dirname(__FILE__).'/top/request/AlibabaAliqinFcTtsNumSinglecallRequest.php';
require dirname(__FILE__).'/top/request/AlibabaAliqinFcVoiceNumDoublecallRequest.php';
require dirname(__FILE__).'/top/request/AlibabaAliqinFcVoiceNumSinglecallRequest.php';
class Sms{

	/*
	 *
	 */
	public function send($tel, $sign, $template,$param='',  $userId=0){
		$sessionkey = '';
	
		$c = new TopClient;
		$c->appkey = ALISMS_APPKEY;
		$c->secretKey = ALISMS_SECRETKEY;
		$req = new AlibabaAliqinFcSmsNumSendRequest;

		if($userId){
			$req->setExtend($userId);  				// 公共回传参数，在“消息返回”中会透传回该参数；举例：用户可以传入自己下级的会员ID，在消息返回时，该会员ID会包含在内，用户可以根据该会员ID识别是哪位会员使用了你的应用
		}
		$req->setSmsType("normal"); 				// 短信类型，传入值请填写normal
		$req->setSmsFreeSignName($sign); 			// 短信签名，传入的短信签名必须是在阿里大鱼“管理中心-短信签名管理”中的可用签名
		if($param!=''){
			$req->setSmsParam($param);	// 短信模板变量，传参规则{"key"："value"}，key的名字须和申请模板中的变量名一致，多个变量之间以逗号隔开，示例：{"name":"xiaoming","code":"1234"}
		}
		$req->setRecNum($tel);				// 手机号
		$req->setSmsTemplateCode($template);	// 短信模板ID，传入的模板必须是在阿里大鱼阿里大鱼“管理中心-短信模板管理”中的可用模板
		$resp = $c->execute($req, $sessionkey);

		return $resp;
	}
}

