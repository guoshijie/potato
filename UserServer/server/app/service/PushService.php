<?php
/**
 * 消息推送
 * User: guoshijie
 * Date: 15/12/17
 * Time: 下午1:53
 */
namespace App\Service;

use AndroidCustomizedcast;
use AndroidUnicast;
use IOSCustomizedcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class PushService {

    private $timestamp = NULL;
    private $production_mode = NULL;
    const AppKey = '560949fbe0f55a9111003080';
    const AppMasterSecret = 'hgxtrl2wrndyuoukgbx1ro2j7qc0kihf';

    public function __construct(){
        $this->timestamp = strval(time());
        $this->production_mode = App::environment() == 'local'?false:true;
    }

    /**
     * IOS自定义播(customizedcast)
     * 开发者通过自有的alias进行推送, 可以针对单个或者一批alias进行推送，也可以将alias存放到文件进行发送
     */
    public function sendIOSCustomizedcast($data) {
        try {
            $customizedcast = new IOSCustomizedcast();
            $customizedcast->setAppMasterSecret(self::AppMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey",           self::AppKey);
            $customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

            $customizedcast->setPredefinedKeyValue("alias", $data['alias']);
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", $data['alias_type']);
            $customizedcast->setPredefinedKeyValue("alert", $data['text']);
//            $customizedcast->setPredefinedKeyValue("badge", 0);
//            $customizedcast->setPredefinedKeyValue("sound", "chime");

            $customizedcast->setPredefinedKeyValue("production_mode", $this->production_mode);
            Log::info("Sending customizedcast notification, please wait...\r\n");
            $result = $customizedcast->send();
            Log::info("Sent SUCCESS\r\n".$result."\r\n\r\n");
            return $result;
        } catch (Exception $e) {
            Log::error("Caught exception: " . $e->getMessage());
        }
    }

    /**
     * Android自定义播(customizedcast)
     * 开发者通过自有的alias进行推送, 可以针对单个或者一批alias进行推送，也可以将alias存放到文件进行发送
     * @param $data
     * @return mixed
     * @throws \Exception
     * @internal param $alias
     * @internal param $alias_type
     */
    public function sendAndroidCustomizedcast($data) {
        try {
            $customizedcast = new AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret(self::AppMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey",           self::AppKey);
            $customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

            $customizedcast->setPredefinedKeyValue("alias",            $data['alias']);
            $customizedcast->setPredefinedKeyValue("alias_type",       $data['alias_type']);

            $customizedcast->setPredefinedKeyValue("ticker",           $data['ticker']);
            $customizedcast->setPredefinedKeyValue("title",            $data['title']);
            $customizedcast->setPredefinedKeyValue("text",             $data['text']);
            $customizedcast->setPredefinedKeyValue("after_open",       $data['after_open']);

            $customizedcast->setPredefinedKeyValue("production_mode", $this->production_mode);

            $customizedcast->setExtraField("display_type",'notification');
            $customizedcast->setExtraField("period_number",$data['period_number']);
            $customizedcast->setExtraField("goods_name",$data['goods_name']);
            $customizedcast->setExtraField("msg_type",$data['msg_type']);

            $result = $customizedcast->send();
            Log::info("Sent SUCCESS\r\n".$result."\r\n\r\n");
            return $result;
        } catch (Exception $e) {
            Log::error("Caught exception: " . $e->getMessage());
        }
    }



    /**
     * Android发送红包自定义播(customizedcast)
     * 开发者通过自有的alias进行推送, 可以针对单个或者一批alias进行推送，也可以将alias存放到文件进行发送
     * @param $data
     * @return mixed
     * @throws \Exception
     * @internal param $alias
     * @internal param $alias_type
     */
    public function sendAndroidCustomizedcastByRedpacket($data) {
        try {
            $customizedcast = new AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret(self::AppMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey",           self::AppKey);
            $customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

            $customizedcast->setPredefinedKeyValue("alias",            $data['alias']);
            $customizedcast->setPredefinedKeyValue("alias_type",       $data['alias_type']);

            $customizedcast->setPredefinedKeyValue("ticker",           $data['ticker']);
            $customizedcast->setPredefinedKeyValue("title",            $data['title']);
            $customizedcast->setPredefinedKeyValue("text",             $data['text']);
            $customizedcast->setPredefinedKeyValue("after_open",       $data['after_open']);
            $customizedcast->setPredefinedKeyValue("activity",         $data['activity']);

            $customizedcast->setPredefinedKeyValue("production_mode", $this->production_mode);

            $customizedcast->setExtraField("display_type",'notification');

            $result = $customizedcast->send();
            Log::info("Sent SUCCESS\r\n".$result."\r\n\r\n");
            return $result;
        } catch (Exception $e) {
            Log::error("Caught exception: " . $e->getMessage());
        }
    }

    /**
     * Android单播(unicast)
     * 向指定的设备发送消息，包括向单个device_token或者单个alias发消息
     * @throws \Exception
     */
    public function anySendAndroidUnicast() {
        try {
            $unicast = new AndroidUnicast();
            $unicast->setAppMasterSecret(self::AppMasterSecret);
            $unicast->setPredefinedKeyValue("appkey",           self::AppKey);
            $unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);

            $unicast->setPredefinedKeyValue("device_tokens",    "AgaJIgr1Jeikv9YSOS42iVGd9qX9Vt_16FGDd8S7dIBx");
            $unicast->setPredefinedKeyValue("ticker",           "Android unicast ticker");
            $unicast->setPredefinedKeyValue("title",            "Android unicast title");
            $unicast->setPredefinedKeyValue("text",             "Android unicast text");
            $unicast->setPredefinedKeyValue("after_open",       "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", $this->production_mode);
            // Set extra fields
            $unicast->setExtraField("test", "helloworld");
            Log::info("Sending unicast notification, please wait...\r\n");
            $unicast->send();
            Log::info("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            Log::error("Caught exception: " . $e->getMessage());
        }
    }



    public function sendPushByUserPhoneType($type,$data){
        if($type == 10){   //android
            $this->sendAndroidCustomizedcastByRedpacket($data);
        }elseif($type == 01){   //ios
            $this->sendIOSCustomizedcast($data);
        }elseif($type == 11){  //android   and ios
            $this->sendAndroidCustomizedcastByRedpacket($data);
            $this->sendIOSCustomizedcast($data);
        }else{
            Log::error("用户类型os_type不正常".$type);
            return false;
        }
    }

}