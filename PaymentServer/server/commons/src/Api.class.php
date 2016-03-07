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
        $this->pathSuffix = $pathSuffix;
    }

    public function getData($uri)
    {
        $out = $this->curl->get($this->pathSuffix.$uri);
        $json = $this->str2json($out);
        if ($json["code"] == 200) {
            return $json["data"];
        } else {
            throw new \Exception($json["message"]);
        }
    }

    public function postData($uri, $vars = array())
    {
        $out = $this->curl->post($this->pathSuffix.$uri, $vars);
        $json = $this->str2json($out);
        if ($json["code"] == 200) {
            return $json["data"];
        } else {
            throw new \Exception($json["message"]);
        }
    }

    private function str2json($str)
    {
        return json_decode($str,true);
    }
}