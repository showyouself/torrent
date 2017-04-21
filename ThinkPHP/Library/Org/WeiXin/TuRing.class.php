<?php
namespace Org\WeiXin;
class TuRing{
    /**
     * 请求图灵机器人回复
     * @param 必填  $info 
     */
    public function getanswer($info){
        $curl=new Curl();
        $answer=$curl->rapid("http://www.tuling123.com/openapi/api?key=158e7cd28a167b15d1cc34cd599e22f8&info={$info}");
        return $answer;
    }
}