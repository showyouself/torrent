<?php
namespace Org\WeiXin;
class WeiXinPay{
    /**
     * 使用此方法请在数据库建立【pre_paylog】表保存预支付数据openid/out_trade_no/productid
     * @author zengbin 2016-03-11
     */
    public function __construct(){
        $this->pre_paylog=M('pre_paylog');
    }
    /**
     * 生成支付预支付的jsdk/随机生成32位商户订单
     * @param  必填 $openid 
     * @param  必填 $money  付多少钱  
     * @param  可选 $pname   商品名称
     * @param  可选 $Notifyurl 支付结果回调URL，注：支付成功后 微信服务器 发送【支付结果】到这个URL
     * @return 返回对象，jsdk存放在result中，商户订单存放在out_trade_no中
     */
    public function getJssdk($openid,$money,$pname="没有名字",$Notifyurl="null"){
        Vendor('WeiXinPay.WxPayApi');
	    Vendor('WeiXinPay.JsApiPay');
	    $inputObj= new \WxPayUnifiedOrder();
	    $input=new \WxPayApi();
	    $jstool=new \JsApiPay();
	    $random=$input->getNonceStr();
	    $inputObj->SetBody($pname);
	    $inputObj->SetTotal_fee($money*100);
	    $inputObj->SetOut_trade_no($random);
	    $inputObj->SetTrade_type("JSAPI");
	    $inputObj->SetOpenid($openid);
	    if($Notifyurl!="null"){
	        $inputObj->SetNotify_url($Notifyurl);
	    }
	    $result= $input->unifiedOrder($inputObj);
	    $result=$jstool->GetJsApiParameters($result);
	    //保存用户预支付数据-防止漏单
	    $data['productid']="jsdk";
	    $data['out_trade_no']=$random;
	    $data['openid']=$openid;
	    $data['add_time']=time();
	    $this->pre_paylog->data($data)->add();
	    //保存用户预支付数据-end
	    $bak['result']=$result;//页面使用的js代码;
	    $bak['out_trade_no']=$random;//商户订单号
	    return $bak;
    }
    
    /**
     * 扫码支付情况-生成预支付的二维码
     * @param  必填  $money  付多少钱 
     * @param  必填  $productid 商品id 
     * @param  可选 $pname  商品名称
     * @param  可选 $Notifyurl  支付结果回调URL，注：支付成功后 微信服务器 发送【支付结果】到这个URL
     * @return 返回对象，结果存放在result中，商户订单存放在out_trade_no中,二维码信息在qcode中
     */
    public function getPayQcode($money,$productid,$pname="没有名字",$Notifyurl="null"){
        Vendor('WeiXinPay.WxPayApi');
	    Vendor('WeiXinPay.WxPayNativePay');
	    $input=new \WxPayApi();
	    $inputObj= new \WxPayUnifiedOrder();
	    $notify = new \NativePay();
	    $random=$input->getNonceStr();
	    $inputObj->SetBody($pname);
	    $inputObj->SetOut_trade_no($random);
	    $inputObj->SetTotal_fee($money*100);
	    $inputObj->SetTrade_type("NATIVE");
	    $inputObj->SetProduct_id($productid);
	    if($Notifyurl!="null"){
	        $inputObj->SetNotify_url($Notifyurl);
	    }
	    $result = $notify->GetPayUrl($inputObj);
	    //保存用户预支付数据-防止漏单
	    $data['productid']=$productid;
	    $data['out_trade_no']=$random;
	    $data['openid']="qcode";
	    $data['add_time']=time();
	    $this->pre_paylog->data($data)->add();
	    //保存用户预支付数据-end
	    $bak['result']=$result;//页面使用的js代码;
	    $bak['qcode']=$result['code_url'];//页面使用的js代码;
	    $bak['out_trade_no']=$random;//商户订单号
	    return $bak;
    }
    
    
    /**
     * 回调函数模板--用于记录支付的记录信息
     * 异步接收支付结果-微信请求接口
     * 注意：使用这个模板，请保证前3个【表/文件】已经创建。
     */
    public function getNotify(){
        //3个【表/文件】
        $log="C:/wamp/www/paylog.txt";//日志文件地址
        $this->paylog=M('paylog');  //存放支付成功后的表
        $this->pre_paylog=M('pre_paylog');  //存放预支付的表
        //3个【表/文件】-end
        
        header('Content-Type: text/html; charset=utf-8');
        $timezone="Asia/Shanghai";
        date_default_timezone_set($timezone); //北京时间
        //$GLOBALS['HTTP_RAW_POST_DATA'] 微信采用的xml发送的post  只能采用原始数据获取
        $msg = array();
        $msg = (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);
        extract($msg);//数组转换成变量
        if($result_code=='SUCCESS'){
            // 更新你的数据库--验证签名
            if($trade_type!="NATIVE"){//扫码【NATIVE】时 预支付表是不会存openid的
                $where['openid']=$openid;
            }
            $where['openid']=$openid;
            $where['out_trade_no']=$out_trade_no;
            $is_pre=$this->pre_paylog->where($where)->find();
            $is_pay=$this->paylog->where($where)->find();
            if($is_pre&&!$is_pay){// 支付成功，只保存一次支付记录
                //日志文件 txt
                ob_start();
                echo "\r\n------------------------------------------------\r\n";
                echo $GLOBALS['HTTP_RAW_POST_DATA'];
                $ob = ob_get_contents();
                file_put_contents($log,$ob,FILE_APPEND);
                ob_end_clean();
                //日志文件---end
                //更新数据库 -paylog
                $data['openid']=$openid;
                $data['is_subscribe']=$is_subscribe;
                $data['cash_fee']=$cash_fee;
                $data['out_trade_no']=$out_trade_no;
                $data['return_code']=$return_code;
                $data['sign']=$sign;
                $data['time_end']=$time_end;
                $data['total_fee']=$total_fee;
                $data['transaction_id']=$transaction_id;
                $data['add_time']=time();
                $this->paylog->data($data)->add();
                //更新数据库 -paylog -end
                
                // 支付成功，额外操作
                $extra="在这里写下额外需要的操作";
                // 支付成功，额外操作-end
                
            }
            // 更新你的数据库--验证签名-end
            echo "success";	//查看是否成功
        }
    }
    
}