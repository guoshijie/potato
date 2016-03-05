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
		return $this->checkData($out, $uri);
    }

    public function postData($uri, $vars = array())
    {
        $out = $this->curl->post($this->pathSuffix.$uri, $vars);
		return $this->checkData($out, $uri, $vars);
    }
	
	public function checkData($out, $uri, $vars=array()){
		if ($this->curl->http_code == 404) {
			$error = "error [404]: can't connect server ". $this->pathSuffix.$uri;
            //throw new \Exception($error);
		}

		if ($this->curl->http_code == 500) {
            $error = "error [500]: server 内部错误  ". $this->pathSuffix.$uri;
            //throw new \Exception($error);
		}

        if ($this->curl->http_code != 200) {
            $error = "error {$this->curl->http_code}: ". $this->pathSuffix.$uri;
            //throw new \Exception($error);
        }
        if(is_null(json_decode($out))){
            $error = 'error: '.$this->pathSuffix .' data is not json';
            //throw new \Exception($error);
        }

        $json = $this->str2json($out);

        if ( !isset($json->code)) {
            $error = 'error:'.$this->pathSuffix .' data  not find json[code]';
            //throw new \Exception($error);

        }

		if(isset($error) && env('APP_DEBUG')){
			die($out);
		}else{
			header('Content-type: application/json');
		}

        // 不加这个头，json解析如果带 html标签会出错
        return isset($error) ? $error : $out;
	
	}

    private function str2json($str)
    {
        return json_decode($str);
    }
}
