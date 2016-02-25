<?php
/**
 * ip服务
 * User: zhangtaichao
 * Date: 15/11/18
 * Time: 下午3:52
 */

namespace App\Service;

use Illuminate\Support\Facades\Log;

class IPService {

    /**
     * @param $ip
     * @return ip对应的省份信息
     */
    public static function find($ip) {
        if(empty($ip)) {
            return null;
        }
//        $res = self::fromTaobao($ip);
//        if(!empty($res)) {
//            return $res;
//        }
//        $res = self::fromipip($ip);
//        if(!empty($res)) {
//            return $res;
//        }
        $res = self::fromLocal($ip);
        if(!empty($res)) {
            return $res;
        }

        return null;
    }

    public static function fromTaobao($ip) {
        try {
            $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
            $res = file_get_contents($url);
            $res = json_decode($res);
            if($res->code != '0') {
                return null;
            } else {
                if($res->data->country == '未分配或者内网IP') {
                    return null;
                }
                $result['country'] = $res->data->country;
                $result['province'] = $res->data->region;
                $result['city'] = $res->data->city;
                $result['county'] = $res->data->county;
                Log::debug("ip=>{$ip};data=>" . var_export($res,true),array('taobao'));
                return $result;
            }
        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }

    }
    public static function fromipip($ip) {
        try {
            $url = "http://freeapi.ipip.net/" . $ip;
            $res = file_get_contents($url);
            if(empty($res)) {
                return null;
            }
            $res = json_decode($res);
            if($res[0] == '保留地址') {
                return null;
            }
            $result['country'] = $res[0];
            $result['province'] = $res[1];
            $result['city'] = $res[2];
            if(!empty($res[3])) {
                $result['county'] = $res[3];
            }

            Log::debug("ip=>{$ip};data=>" . var_export($res,true),array('ipip'));
            return $result;
        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }
    }
    public static function fromLocal($ip) {
        try {
            $ipc = new \IP();
            $res = $ipc->find($ip);
            if($res == 'N/A') {
                return null;
            } else {
                if($res[0] == '保留地址') {
                    return null;
                }
                $result['country'] = $res[0];
                $result['province'] = $res[1];
                $result['city'] = $res[2];
                if(!empty($res[3])) {
                    $result['county'] = $res[3];
                }
                Log::debug("ip=>{$ip};data=>" . var_export($res,true),array('local'));
                return $result;
            }
        } catch(\Exception $e) {
            Log::error($e);
            return null;
        }
    }
}
