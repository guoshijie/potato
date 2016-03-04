<?php namespace Api\Server\AdvertServer;

use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class Banner extends Api
{
    const HOST = "advert.server.potato";

    public function __construct()
    {
        parent::__construct(self::HOST);
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
    public function hello()
    {
        return $this->getData("/api/banner?type=2");
    }

    public function intro()
    {
        return $this->getData("/api/banner?type=5");
    }

}
