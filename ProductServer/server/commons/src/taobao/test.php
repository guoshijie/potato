<?php
	include "TopSdk.php";
	date_default_timezone_set('Asia/Shanghai'); 

	include "top/TopClient.php";
	include "top/request/AlibabaAliqinFcSmsNumSendRequest.php";
	include "top/request/AlibabaAliqinFcTtsNumSinglecallRequest.php";
	include "top/request/AlibabaAliqinFcVoiceNumDoublecallRequest.php";
	include "top/request/AlibabaAliqinFcVoiceNumSinglecallRequest.php";

$c = new TopClient;
$c->appkey = '23262964';
$c->secretKey = '090a4dc4b1bce10c8b87f2910fab99c4';
$req = new AlibabaAliqinFcSmsNumSendRequest;

$req->setExtend("123456");  				// 公共回传参数，在“消息返回”中会透传回该参数；举例：用户可以传入自己下级的会员ID，在消息返回时，该会员ID会包含在内，用户可以根据该会员ID识别是哪位会员使用了你的应用
$req->setSmsType("normal"); 				// 短信类型，传入值请填写normal
$req->setSmsFreeSignName("活动验证"); 			// 短信签名，传入的短信签名必须是在阿里大鱼“管理中心-短信签名管理”中的可用签名
$req->setSmsParam('{"product":"大爷","code":"1234"}');	// 短信模板变量，传参规则{"key"："value"}，key的名字须和申请模板中的变量名一致，多个变量之间以逗号隔开，示例：{"name":"xiaoming","code":"1234"}
$req->setRecNum("15301113728");				// 手机号
$req->setSmsTemplateCode("SMS_2040105");	// 短信模板ID，传入的模板必须是在阿里大鱼阿里大鱼“管理中心-短信模板管理”中的可用模板
$resp = $c->execute($req, $sessionKey);

print_r($resp);
die;

	include "TopSdk.php";
	date_default_timezone_set('Asia/Shanghai'); 

	$httpdns = new HttpdnsGetRequest;
	$client = new ClusterTopClient("23262964","090a4dc4b1bce10c8b87f2910fab99c4");
	$client->gatewayUrl = "https://eco.taobao.com/router/rest";
	var_dump($client->execute($httpdns));

?>