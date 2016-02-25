<?php
/**
 * 系统通知
 * @author guoshijie@shinc.net
 * @version v1.0
 * @copyright shinc
 */

namespace App\Http\Models\System;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class SystemNoticeModel extends Model {
    private $nowDateTime;

    public function __construct(){
        $this->init();
    }

    private function init(){
        $this->nowDateTime = date('Y-m-d H:i:s');
    }

    public function getSystemNoticeList(){
        $list = DB::select('
            select
              id,
              create_time,
              update_time,
              title,
              content
            from
              sh_system_notice
            WHERE
              status = "1"
            order by create_time desc
        ');
        return $list;
    }

    public function getSystemNoticeById($noticeId){
        return DB::table('system_notice')->where('id', $noticeId)->first();
    }

    public function getLatestDate(){
        $res = DB::select('
            select
                create_time
            from
              sh_system_notice
            WHERE
              status = "1"
            order by create_time DESC
        ');
        return $res;
    }

}
