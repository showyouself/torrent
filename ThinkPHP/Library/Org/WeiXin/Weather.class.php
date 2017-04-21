<?php
namespace Org\WeiXin;
class Weather{
    public $city;
    /**
     * 查询天气
     * @param 必填 $city 哪个城市
     */
    public function __construct($city){
        $this->city=$city;
    }
    public function get_weather(){
        $curl=new Curl();
        $result =$curl->rapid("http://api.map.baidu.com/telematics/v3/weather?location={$this->city}&output=json&ak=mvc5k26yPmjLSqI74HdylwsG");
        $result=json_decode($result,true);
        return $result;
    }
}