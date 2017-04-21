<?php
namespace Org\WeiXin;
class News{
    public function __construct(){
        
    }
    /**
     * 此方法已经废除 2016-3-11
     * @param unknown $sort
     */
    public function get_news($sort){
        $curl=new Curl();
        if($sort=='JKZX'){
            $url='http://apis.baidu.com/txapi/health/health';
        }if($sort=='TYXW'){
            $url='http://apis.baidu.com/txapi/tiyu/tiyu';
        }
        if($sort=='YLXW'){
            $url='http://apis.baidu.com/txapi/huabian/newtop';
        }
        $news=$curl->rapid("{$url}?num=6&page=1",
            "GET",
            NULL,
            array(
                "accept: application/json",
                "apikey: 09502e36b2441d4cdfb0b9ed23640177",
                "content-type: application/json"
            ));
        $news=json_decode($news,true);
        return  $news;
    }
}