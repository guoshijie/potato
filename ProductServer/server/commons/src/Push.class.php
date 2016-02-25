<?php
// namespace App\Libraries;
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
// debug(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCustomizedcast.php');

class Push {
	protected $appkey           =  '5599f5ca67e58eb78e000359'; 
	protected $appMasterSecret     = 'uwmhqeztonqdumhisdbgdyfxfheee1ap';
	protected $timestamp        = NULL;
	protected $validation_token = NULL;

	function __construct() {
// 		$this->appkey = $key;
// 		$this->appMasterSecret = $secret;
		$this->timestamp = strval(time());
	}

	//向所有安卓设备推送消息
	function sendAndroidBroadcast($data) {
		try {
			$brocast = new AndroidBroadcast();
			$brocast->setAppMasterSecret($this->appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			$brocast->setPredefinedKeyValue("ticker",           $data['ticker']);
			$brocast->setPredefinedKeyValue("title",            $data['title']);
			$brocast->setPredefinedKeyValue("text",             $data['text']);
			$brocast->setPredefinedKeyValue("description",      $data['description']);
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			$brocast->setPredefinedKeyValue("production_mode", "true");
			// [optional]Set extra fields
			if($data['after_open'] == 'go_app') {
				$brocast->setPredefinedKeyValue("after_open", "go_app");
				if(!empty($data['param']) && is_array($data['param'])){
					foreach ($data['param'] as $v) {
						$brocast->setExtraField($v['key'], $v['value']);
					}
				}
				
			} elseif($data['after_open'] == 'go_url') {
				$brocast->setPredefinedKeyValue("after_open", "go_url");
				$brocast->setPredefinedKeyValue("url", $data['url']);
			}
			
			return $brocast->send();
		} catch (Exception $e) {
			return  "Caught exception: " . $e->getMessage();
		}
	}

	//向单个安卓设备推送消息
	function sendAndroidUnicast($data) {
		try {
			$unicast = new AndroidUnicast();
			$unicast->setAppMasterSecret($this->appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$unicast->setPredefinedKeyValue("device_tokens",    $data['device_tokens']); 
			$unicast->setPredefinedKeyValue("ticker",           $data['ticker']);
			$unicast->setPredefinedKeyValue("title",            $data['title']);
			$unicast->setPredefinedKeyValue("text",             $data['text']);
			$unicast->setPredefinedKeyValue("description",      $data['description']);
			
			$unicast->setPredefinedKeyValue("after_open",       "go_app");
			
			if($data['after_open'] == 'go_app') {
				$unicast->setPredefinedKeyValue("after_open", "go_app");
				if(!empty($data['param']) && is_array($data['param'])){
					foreach ($data['param'] as $v) {
						$unicast->setExtraField($v['key'], $v['value']);
					}
				}
			
			} elseif($data['after_open'] == 'go_url') {
				$unicast->setPredefinedKeyValue("after_open", "go_url");
				$unicast->setPredefinedKeyValue("url", $data['url']);
			}
			
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			$unicast->setPredefinedKeyValue("production_mode", "true");
			// Set extra fields
			return $unicast->send();
		} catch (Exception $e) {
			return "Caught exception: " . $e->getMessage();
		}
	}


	//向友盟统计组用户推送
	function sendAndroidGroupcast($data) {
		try {
			/* 
		 	 *  Construct the filter condition:
		 	 *  "where": 
		 	 *	{
    	 	 *		"and": 
    	 	 *		[
      	 	 *			{"tag":"test"},
      	 	 *			{"tag":"Test"}
    	 	 *		]
		 	 *	}
		 	 */
			$filter = array(
				"where" => array(
					$data['type'] 	=>  array()
				)
			);
			if($data['type'] && !empty($data['tags']) && is_array($data['tags'])) {
				$filter['where'] = array(
					"and" => array(
						'0' => array($data['type'] => array())
					)
				);
				
				$filter['where']['and']['0'][$data['type']] = $data['tags'];
				
			}
			
// 			$filter2 = 	array(
// 					"where" => 	array(
// 							"and" => array(
// 								array(
// 									"or"=>  array(
// 										array("tag" => "A1"),
// 										array("tag" => "A2")
// 									)
								
// 								)
// 							)
							
// 					)
// 			);
			$groupcast = new AndroidGroupcast();
			$groupcast->setAppMasterSecret($this->appMasterSecret);
			$groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set the filter condition
			$groupcast->setPredefinedKeyValue("filter",           $filter);
			$groupcast->setPredefinedKeyValue("ticker",           $data['ticker']);
			$groupcast->setPredefinedKeyValue("title",            $data['title']);
			$groupcast->setPredefinedKeyValue("text",             $data['text']);
			$groupcast->setPredefinedKeyValue("after_open",       "go_app");
			$groupcast->setPredefinedKeyValue("description",      $data['description']);
			
			if($data['after_open'] == 'go_app') {
				$groupcast->setPredefinedKeyValue("after_open", "go_app");
				if(!empty($data['param']) && is_array($data['param'])){
					foreach ($data['param'] as $v) {
						$groupcast->setExtraField($v['key'], $v['value']);
					}
				}
			} elseif($data['after_open'] == 'go_url') {
				$groupcast->setPredefinedKeyValue("after_open", "go_url");
				$groupcast->setPredefinedKeyValue("url", $data['url']);
			}
				
			$groupcast->setPredefinedKeyValue("production_mode", "true");
			print("Sending groupcast notification, please wait...\r\n");
			$groupcast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	//向自定义条件用户推送
	function sendAndroidCustomizedcast() {
		try {
			$customizedcast = new AndroidCustomizedcast();
			$customizedcast->setAppMasterSecret($this->appMasterSecret);
			$customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then 
			// use file_id to send customized notification.
			$customizedcast->setPredefinedKeyValue("alias",            "xx");
			// Set your alias_type here
			$customizedcast->setPredefinedKeyValue("alias_type",       "xx");
			$customizedcast->setPredefinedKeyValue("ticker",           "Android customizedcast ticker");
			$customizedcast->setPredefinedKeyValue("title",            "Android customizedcast title");
			$customizedcast->setPredefinedKeyValue("text",             "Android customizedcast text");
			$customizedcast->setPredefinedKeyValue("after_open",       "go_app");
			print("Sending customizedcast notification, please wait...\r\n");
			$customizedcast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

//====================================== 以下为IOS部分 =============================================================//



	function sendIOSBroadcast() {
		try {
			$brocast = new IOSBroadcast();
			$brocast->setAppMasterSecret($this->appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			$brocast->setPredefinedKeyValue("alert", "IOS 广播测试");
			$brocast->setPredefinedKeyValue("badge", 0);
			$brocast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$brocast->setPredefinedKeyValue("production_mode", "false");
			// Set customized fields
			$brocast->setCustomizedField("test", "helloworld");
			print("Sending broadcast notification, please wait...\r\n");
			$brocast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSUnicast() {
		try {
			$unicast = new IOSUnicast();
			$unicast->setAppMasterSecret($this->appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$unicast->setPredefinedKeyValue("device_tokens",    "xx"); 
			$unicast->setPredefinedKeyValue("alert", "IOS 单播测试");
			$unicast->setPredefinedKeyValue("badge", 0);
			$unicast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$unicast->setPredefinedKeyValue("production_mode", "false");
			// Set customized fields
			$unicast->setCustomizedField("test", "helloworld");
			print("Sending unicast notification, please wait...\r\n");
			$unicast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSFilecast() {
		try {
			$filecast = new IOSFilecast();
			$filecast->setAppMasterSecret($this->appMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->appkey);
			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			$filecast->setPredefinedKeyValue("alert", "IOS 文件播测试");
			$filecast->setPredefinedKeyValue("badge", 0);
			$filecast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$filecast->setPredefinedKeyValue("production_mode", "false");
			print("Uploading file contents, please wait...\r\n");
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents("aa"."\n"."bb");
			print("Sending filecast notification, please wait...\r\n");
			$filecast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSGroupcast() {
		try {
			/* 
		 	 *  Construct the filter condition:
		 	 *  "where": 
		 	 *	{
    	 	 *		"and": 
    	 	 *		[
      	 	 *			{"tag":"iostest"}
    	 	 *		]
		 	 *	}
		 	 */
			$filter = 	array(
							"where" => 	array(
								    		"and" 	=>  array(
								    						array(
							     								"tag" => "iostest"
															)
								     		 			)
								   		)
					  	);
					  
			$groupcast = new IOSGroupcast();
			$groupcast->setAppMasterSecret($this->appMasterSecret);
			$groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set the filter condition
			$groupcast->setPredefinedKeyValue("filter",           $filter);
			$groupcast->setPredefinedKeyValue("alert", "IOS 组播测试");
			$groupcast->setPredefinedKeyValue("badge", 0);
			$groupcast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$groupcast->setPredefinedKeyValue("production_mode", "false");
			print("Sending groupcast notification, please wait...\r\n");
			$groupcast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSCustomizedcast() {
		try {
			$customizedcast = new IOSCustomizedcast();
			$customizedcast->setAppMasterSecret($this->appMasterSecret);
			$customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then 
			// use file_id to send customized notification.
			$customizedcast->setPredefinedKeyValue("alias", "xx");
			// Set your alias_type here
			$customizedcast->setPredefinedKeyValue("alias_type", "xx");
			$customizedcast->setPredefinedKeyValue("alert", "IOS 个性化测试");
			$customizedcast->setPredefinedKeyValue("badge", 0);
			$customizedcast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$customizedcast->setPredefinedKeyValue("production_mode", "false");
			print("Sending customizedcast notification, please wait...\r\n");
			$customizedcast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}
}

// Set your appkey and master secret here
// $demo = new Demo("your appkey", "your app master secret");
// $demo->sendAndroidUnicast();
/* these methods are all available, just fill in some fields and do the test
 * $demo->sendAndroidBroadcast();
 * $demo->sendAndroidFilecast();
 * $demo->sendAndroidGroupcast();
 * $demo->sendAndroidCustomizedcast();
 *
 * $demo->sendIOSBroadcast();
 * $demo->sendIOSUnicast();
 * $demo->sendIOSFilecast();
 * $demo->sendIOSGroupcast();
 * $demo->sendIOSCustomizedcast();
 */