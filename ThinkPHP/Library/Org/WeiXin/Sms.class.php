<?php
namespace Org\WeiXin;
class Sms{
    /**
     * @author zb
     * @param 必选string $phone 接收人手机号
     * @param 必选array $content 例如：array('code'=>code,'product'=>product,'item'=>item);
     * @param 可选string $id 自主定义的ID
     * @param 可选string $Templid  SMS_4715868--SMS_4715874  对应：信息变更,修改密码,活动确认,用户注册,登录异常,登录确认,身份验证
     * @param 可选string $SignName  活动验证，变更验证，登录验证，注册验证，身份验证
     */
public function sendSms($phone,$content,$id="null",$Templid="SMS_5450159",$SignName="身份验证"){
    $content=json_encode($content);
    Vendor('bigfish.Autoloader');
    $c = new \TopClient(); 
    $c->appkey = "23318900";
    $c->secretKey = "15fcb40ba2eb3238cd73913d1816160b";
    $req = new \AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend($id);
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($SignName);
    $req->setSmsParam($content);
    $req->setRecNum($phone);
    $req->setSmsTemplateCode($Templid);
    $resp = $c->execute($req);
    return $resp;
    }
} 
        
   