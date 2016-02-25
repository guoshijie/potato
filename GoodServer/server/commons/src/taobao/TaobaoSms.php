<?php
namespace App\Libraries;
/* *
* 类名：阿里大鱼短信平台
* 功能：给手机发送短信信息
* 详细：验证码等
*/
//输入输出类
require app_path().'/libraries/class/taobao/TopSdk.php';
//date_default_timezone_set('Asia/Shanghai'); 

require app_path().'/libraries/class/taobao/top/TopClient.php';
require app_path().'/libraries/class/taobao/top/request/AlibabaAliqinFcSmsNumSendRequest.php';
require app_path().'/libraries/class/taobao/top/request/AlibabaAliqinFcTtsNumSinglecallRequest.php';
require app_path().'/libraries/class/taobao/top/request/AlibabaAliqinFcVoiceNumDoublecallRequest.php';
require app_path().'/libraries/class/taobao/top/request/AlibabaAliqinFcVoiceNumSinglecallRequest.php';
class TaobaoSms{

	public function send($tel, $userId, $product){
		$code = mt_rand(1111, 9999);
	
		$c = new TopClient;
		$c->appkey = '23262964';
		$c->secretKey = '090a4dc4b1bce10c8b87f2910fab99c4';
		$req = new AlibabaAliqinFcSmsNumSendRequest;

		$req->setExtend($userId);  				// 公共回传参数，在“消息返回”中会透传回该参数；举例：用户可以传入自己下级的会员ID，在消息返回时，该会员ID会包含在内，用户可以根据该会员ID识别是哪位会员使用了你的应用
		$req->setSmsType("normal"); 				// 短信类型，传入值请填写normal
		$req->setSmsFreeSignName("活动验证"); 			// 短信签名，传入的短信签名必须是在阿里大鱼“管理中心-短信签名管理”中的可用签名
		$req->setSmsParam('{"product":'.$product.',"code":'.$code.'}');	// 短信模板变量，传参规则{"key"："value"}，key的名字须和申请模板中的变量名一致，多个变量之间以逗号隔开，示例：{"name":"xiaoming","code":"1234"}
		$req->setRecNum($tel);				// 手机号
		$req->setSmsTemplateCode("SMS_2040105");	// 短信模板ID，传入的模板必须是在阿里大鱼阿里大鱼“管理中心-短信模板管理”中的可用模板
		$resp = $c->execute($req, $sessionKey);

		return $resp->success;

	}
}

