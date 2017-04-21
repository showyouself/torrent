<?php
namespace Org\WeiXin;
class Oil{
    /**
     * 获取油价信息
     */
    public function getprice(){
        $curl=new Curl();
        $oil=$curl->rapid("http://apis.baidu.com/showapi_open_bus/oil_price/find?prov=江西",
            "GET",
            NULL,
            array(
                "accept: application/json",
                "apikey: 09502e36b2441d4cdfb0b9ed23640177",
                "content-type: application/json"
            ));
        $oil=json_decode($oil,true);
        return  $oil;
    }
}