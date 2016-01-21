<?php
namespace Api\Server;

use App\Libraries\Api;
use App\Libraries\Curl;

class UserServer extends Api
{
    const TEST = "TEST";
    const HOST = "user.server.host:20200";

    private $curl;

    public function __construct()
    {
        parent::__construct(UserServer::HOST);
    }

    /**
     *  <code>
     *  {
     *      "user"  : {
     *          "id":1,
     *          "name":"name"
     *      }
     *  }
     * </code>
     * @param $id
     * @return int 自增ID
     */
    public function get($id)
    {
        return $this->getData("/api/user/get?id=" . $id);
    }

    /**
     * <code>
     *  {
     *      "user"  : {
     *          "id":1,
     *          "name":"name"
     *      }
     *  }
     * </code>
     * @param $id 用户ID
     * @param $name 用户姓名
     * @return array
     */
    public function create($id, $name)
    {
        return $this->postData("/api/user/create", array(
            "id" => $id,
            "name" => $name
        ));
    }
}