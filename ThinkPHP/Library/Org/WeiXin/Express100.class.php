<?php
namespace Org\WeiXin;
class Express100{
    public $number;
    /**
     * 快递100查询
     * @param 必填 $number 快递单号，自动识别
     */
    public function __construct($number){
        $this->number=$number;
    }
    public function get_detail(){
       $curl=new Curl();
       $comCode= $curl->rapid("http://www.kuaidi100.com/autonumber/autoComNum?text={$this->number}","POST");
       $comCode=json_decode($comCode,true);
       $comCode=$comCode['auto']['0']['comCode'];//只需要快递代码
       $result=$curl->rapid("http://www.kuaidi100.com/query?type={$comCode}&postid={$this->number}&id=1&valicode=&temp=0.4649930214509318",
       "GET",
       NULL,
       array('Host: www.kuaidi100.com', 
             'Referer: http://www.kuaidi100.com/',
             'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36'
       )
   );
       $result=json_decode($result,true);
       return $result;//$comCode;
    }
    
}