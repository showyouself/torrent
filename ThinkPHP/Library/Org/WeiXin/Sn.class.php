<?php
namespace Org\WeiXin;
class Sn{
    public $appKey ='3132fcae40e146d15e76d80f87bcf25c';
    public function checkSn($sn,$openid='null'){
        //区分是imei 还是 sn
        $url="http://a.apix.cn/3023/apple/apple.html?sn={$sn}";
        $key="apix-key:3132fcae40e146d15e76d80f87bcf25c";
        $urlimei="http://apis.baidu.com/3023/imei/applemobile?imei={$sn}";
        $keyimei="apikey:b266305a9c047031690be19c4e7a46ac";
        if(preg_match_all("/^[\d]{8,}$/i",$sn)){
            $url=$urlimei;
            $key=$keyimei;
        }
        //区分是imei 还是 sn-end
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "{$key}",
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $response=json_decode($response,true);
        if($response['sn']){
            $data['sn']=$response['sn'];
            $data['openid']=$openid;
            $data['add_time']=time();
            M('sn_record')->data($data)->add();
        }
        return $response;
    }
}