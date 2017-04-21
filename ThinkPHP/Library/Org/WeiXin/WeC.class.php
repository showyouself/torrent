<?php
namespace Org\WeiXin;
class WeC{
    //设置全局的id与密码
    public $appid;
    public $secret;
    function __construct($appid="wxefad0428a0f6c030",$secret="2974f877e3cb6ca1b7d20e621f76f9dd") {
        $this->appid=$appid;
        $this->secret=$secret;
    }
    /**
     *  获取access_token
     * @param boolean $timeStamp 是否返回时间戳
     * @return 返回token，$timeStamp为true则返回数组
     */
    public function getToken($timeStamp){
        $appid= $this->appid;
        $secret=$this->secret;
        $curl=new Curl();
        $result= $curl->rapid("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}");
        echo $result;
        $result=json_decode($result,true);
        if($timeStamp==true){
            $result['expires_in']=time();
            //返回数组
            return $result;
        }else{
            return $result['access_token'];
        }
    }
    /**
     *  获取用户列表
     * @param string $token 默认调用最新的token
     * @return 返回数组
     */
    public function  getUserList($token="null",$first_openid=""){
         //如果未输入token,默认调用重新获取一个新的的token
        if(strtolower($token)=="null"){
           $token=$this->getToken();
        }
        if($first_openid!=""){
            $next_openid=$first_openid;
        }
        $curl=new Curl();
        $result=$curl->rapid("https://api.weixin.qq.com/cgi-bin/user/get?access_token={$token}&next_openid={$next_openid}");
        $result=json_decode($result,true);
        return $result;
    }
    
    
    /**
     *  主动发送消息给用户/客服接口-发消息
     * @param string $openid    被发送的用户ID
     * @param string $msgtype   内容类型text/image
     * @param string $content   消息内容
     * @param string $token     默认调用最新的token
     */
    public function sendMessage($openid,$msgtype,$content,$token="null"){
         //如果未输入token,默认调用重新获取一个新的的token
        if(strtolower($token)=="null"){
            $token=$this->getToken();
        }
       if($msgtype=="text"){
           //调用发送文本的方法
          return $this->sendText($openid, $msgtype, $content,$token);
       }
       if($msgtype=='image'){
           
       }
    }
   /**
    *  本方法不推荐使用，请使用sendMessage
    * @param string $openid
    * @param string $msgtype
    * @param string $content
    * @param string $token
    */
   public function sendText($openid,$msgtype,$content,$token="null"){
       $curl=new Curl();
       //拼接发送信息的JSON数据
       $data=array(
           'touser'=>$openid,
           'msgtype'=>$msgtype,
           'text'=>array(
               'content'=>$content
           )
       );
       $data=json_encode($data);
       var_dump($data);
       //发送请求
       $result=$curl->rapid("https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}","POST",$data);
       $result=json_decode($result,true);
       return $result['errcode'];
   }
    
   /**
    *  获取素材列表
    * @param string $type 素材的类型image/video/voice/news
    * @param string $offset
    * @param string $count
    * @param string $token 默认调用最新的token
    * @return mixed
    */
    public function getMediaList($type,$offset='0',$count='20',$token="null"){
         //如果未输入token,默认调用重新获取一个新的的token
        if(strtolower($token)=="null"){
            $token=$this->getToken();
        }
        $curl=new Curl();
        $data=array(
            "type"=>$type,
            'offset'=>$offset,
            'count'=>$count
        );
        $data=json_encode($data);
       //var_dump($data);
       $result= $curl->rapid("https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$token}","POST",$data);
       $result=json_decode($result,true);
       return $result; 
    }
    
    /**
     * 上传文件 该方法需要PHP5.6以上版本
     * @param string $filePath
     * @param string $token
     * @return unknown
     */
    public function uploadMedia($filePath,$token="null"){
        //如果未输入token,默认调用重新获取一个新的的token
        if(strtolower($token)=="null"){
            $token=$this->getToken();
        }
        $data=array('name'=>'file',
            'file'=>new \CURLFile(realpath($filePath))
        );
        echo $filePath;
        $curl=new Curl();
        $result= $curl->rapid("https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$token}&type=image","POST",$data);
        return $result;
    }
        /**
         *  获取永久二维码
         * @param  string $token access_token
         * @param  string $data 二维码详细信息
         * @param  string $dataType 信息类型:int(1-10000)/string(长度限制为1到64) 
         * @return 返回数组，其中picUrl是完整的二维码图片地址
         */
      public function twoDimensionCode($token="",$data="",$dataType="string"){
           //如果未输入token,默认调用重新获取一个新的的token
          if(strtolower($token)=="null"){
              $token=$this->getToken();
          }
             $curl=new Curl();
             $action_name='QR_LIMIT_SCENE';
             //判断场景值ID属性
             if($dataType=="int"){
                 $dataType='scene_id';
             }else{
                 $action_name='QR_LIMIT_STR_SCENE';
                 $dataType='scene_str';
             }
             //拼接永久二维码JSON数据
             $postData=array(
                 'action_name'=>$action_name,
                     'action_info'=>array(
                         'scene'=>array(
                             $dataType=>$data
                         )
                     )
             );
             $data=json_encode($postData);
             //发送请求
             $result= $curl->rapid("https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$token}","POST",$data);
             $result=json_decode($result,true);
             //添加完整图片链接
             $result['picUrl']="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$result['ticket'];
             return $result;
        }
        /**
         * 返回 jsdk 初始化wx.config的 js字符串
         */
        public function getJsdk($debug="false"){
            Vendor('wechatsdk.jssdk');
            $jssdk=new \JSSDK($this->appid, $this->appSecret);
            $signPackage=$jssdk->GetSignPackage();
            $weConf= <<<EOF
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
wx.config({
    debug: {$debug},
    appId: '{$signPackage["appId"]}',
    timestamp: {$signPackage["timestamp"]},
    nonceStr: '{$signPackage["nonceStr"]}',
    signature: '{$signPackage["signature"]}',
    jsApiList: [
		'onMenuShareAppMessage',
		'closeWindow',
		'onMenuShareTimeline',
    ]  });
</script>
EOF;
            return $weConf;
        }
        public function sendTplMsg(){
            
        }
}

