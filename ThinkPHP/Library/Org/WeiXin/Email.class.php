<?php
namespace Org\WeiXin;
class Email{
    /**
     * 请确认表email_conf已经创建    
     * 
     * 字段： MAIL_HOST/MAIL_USERNAME/MAIL_FROM/MAIL_FROMNAME/MAIL_PASSWORD/MAIL_CHARSET
     * @param 必填 $to
     * @param 必填 $title
     * @param 必填 $content
     * @return boolean
     */
    public function sendMail($to,$title,$content){
        $this->email_conf=M('email_conf');
        $email_conf=$this->email_conf->where('id = 1')->find();
        //var_dump($email_conf);
        //exit();
        Vendor('PHPMailer.PHPMailerAutoload');
        $mail = new \PHPMailer(); //实例化
        $mail->IsSMTP(); // 启用SMTP
        $mail->Host=$email_conf['mail_host'];//C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
        $mail->SMTPAuth = true;//C('MAIL_SMTPAUTH'); //启用smtp认证
        $mail->Username = $email_conf['mail_username'];//C('MAIL_USERNAME'); //你的邮箱名
        $mail->Password = $email_conf['mail_password'];//C('MAIL_PASSWORD') ; //邮箱密码
        $mail->From = $email_conf['mail_from'];//C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
        $mail->FromName = $email_conf['mail_fromname'];//C('MAIL_FROMNAME'); //发件人姓名
        $mail->AddAddress($to,"尊敬的客户");
        $mail->WordWrap = 50; //设置每行字符长度
        $mail->IsHTML(TRUE); // 是否HTML格式邮件
        $mail->CharSet=$email_conf['mail_charset'];//C('MAIL_CHARSET'); //设置邮件编码
        $mail->Subject =$title; //邮件主题
        $mail->Body = $content; //邮件内容
        $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
        return($mail->Send());
    }
}