<?php
/**
 * 邮件工具类
 *
 * - 基于PHPMailer的邮件发送
 *
 *  配置
 *
 * 'PHPMailer' => array(
 *   'email' => array(
 *       'smtpsecure' => 'ssl',
 *       'host' => 'smtp.gmail.com',
 *       'port' => '465',
 *       'username' => 'XXX@gmail.com',
 *       'password' => '******',
 *       'from' => 'XXX@gmail.com',
 *       'fromName' => 'PhalApi团队',
 *       'sign' => '<br/><br/>请不要回复此邮件，谢谢！<br/><br/>-- PhalApi团队敬上 ',
 *   ),
 * ),
 *
 * 示例
 *
 * $mailer = new PHPMailer_Lite(true);
 * $mailer->send('chanzonghuang@gmail.com', 'Test PHPMailer Lite', 'something here ...');
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-2-14
 */
header("Content-type: text/html; charset=utf-8");
//require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . 'PHPMailerAutoload.php';
class PHPMailer_Lite{

    protected $debug;

    protected $config;

    public function __construct($debug = FALSE){
        $this->debug = $debug;

        $this->config = DI()->config->get('app.PHPMailer.email');
    }

    public function send($addresses, $title, $content, $isHtml = TRUE){
        require_once('class.phpmailer.php');
        require_once("class.smtp.php");
        $mail  = new PHPMailer();
        $cfg = $this->config;

        $mail->CharSet    ="UTF-8";                 //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置为 UTF-8
        $mail->IsSMTP();                            // 设定使用SMTP服务
        $mail->SMTPAuth   = true;                   // 启用 SMTP 验证功能
        $mail->SMTPSecure = $cfg['smtpsecure'];     // SMTP 安全协议
        $mail->Host       = $cfg['host'];           // SMTP 服务器
        $mail->Port       = $cfg['port'];           // SMTP服务器的端口号
        $mail->Username   = $cfg['username'];       // SMTP服务器用户名
        $mail->Password   = $cfg['password'];       // SMTP服务器密码
        $mail->SetFrom($cfg['from'], $cfg['fromName']);
                                                    // 设置发件人地址和名称
        $mail->AddReplyTo("","");                   // 设置邮件回复人地址和名称
        $mail->Subject    = $title;                 // 设置邮件标题
        $mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";
                                                    // 可选项，向下兼容考虑
        $mail->MsgHTML($content.$cfg['sign']);      // 设置邮件内容
        $mail->AddAddress($addresses, "午安用户");
        //$mail->AddAttachment("images/phpmailer.gif"); // 附件
        if(!$mail->Send()) {
            if ($this->debug) {
                DI()->logger->debug('Fail to send email with error: ' . $mail->ErrorInfo);
            }
            //echo "发送失败：" . $mail->ErrorInfo;
            return false;
        } else {
            if ($this->debug) {
                DI()->logger->debug('Succeed to send email', array('addresses' => $addresses, 'title' => $title, 'content' => $content));
            }
            //echo "恭喜，邮件发送成功！";
            return true;
        }
    }
}
?>