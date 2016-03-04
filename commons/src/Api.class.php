<?php namespace App\Libraries;
use App\Libraries\Curl;

class Api
{

    private $curl;
    private $pathSuffix;

    /**
     * Api constructor.
     */
    public function __construct($pathSuffix)
    {
        $this->curl = new Curl();
		if( !strpos('http://', $pathSuffix) && !strpos('https://', $pathSuffix) ) {
			$this->pathSuffix = 'http://';
		}
        $this->pathSuffix .= $pathSuffix;
    }

    public function getData($uri)
    {
        $out = $this->curl->get($this->pathSuffix.$uri);

        if ($this->curl->http_code != 200) {
            throw new \Exception("error: can't connect server-". $this->pathSuffix);
        }
        if(is_null(json_decode($out))){
            if(env('APP_DEBUG')){
                var_dump($out);
            }
            throw new \Exception('error: '.$this->pathSuffix .' data is not json');
        }

        $json = $this->str2json($out);

        if ( !isset($json["code"])) {
            if(env('APP_DEBUG')){
                echo $out;
            }
            throw new \Exception('error:'.$this->pathSuffix .' data  not find json[code]');

        }

        // 不加这个头，json解析如果带 html标签会出错
		header('Content-type: application/json');
        return $out;
    }

    public function postData($uri, $vars = array())
    {
        $out = $this->curl->post($this->pathSuffix.$uri, $vars);
        $json = $this->str2json($out);
        if ($json["code"] == 200) {
            return $json["data"];
        } else {
            throw new \Exception($json["msg"]);
        }
    }

    private function str2json($str)
    {
        return json_decode($str,true);
    }
}
