<?php namespace Api\Server;

use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class AutoId extends Api
{
    const HOST = "auto.id.host";

    public function __construct()
    {
        parent::__construct(AutoId::HOST);
    }

    /**
     * <code>
     *  data:{
     *      "id":1,
     *      "type":"TEST"
     *  }
     * </code>
     * @param $type 自增ID的类型
     * @return int 自增ID
     */
    public function get($type)
    {
        return $this->getData("/api/auto/id?type=" . $type)["id"];
    }
}
