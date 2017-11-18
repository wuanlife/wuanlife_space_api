<?php
/*
参数	                 默认值	                       选项	                             描述
useragent	        CodeIgniter	                   None	                        用户代理（user agent）
protocol	           mail	                mail, sendmail, or smtp	                邮件发送协议
mailpath	    /usr/sbin/sendmail	                None	                    服务器上 Sendmail 的实际路径
smtp_host	        No Default	                    None	                        SMTP 服务器地址
smtp_user	        No Default	                    None	                        SMTP 用户名
smtp_pass	        No Default	                    None	                        SMTP 密码
smtp_port	            25	                        None	                        SMTP 端口
smtp_timeout	        5	                        None	                    SMTP 超时时间（单位：秒）
smtp_keepalive	      FALSE	                TRUE or FALSE (boolean)	               是否启用 SMTP 持久连接
smtp_crypto	        No Default	                   tls or ssl	                    SMTP 加密方式
wordwrap	           TRUE	                 TRUE or FALSE (boolean)	           是否启用自动换行
wrapchars	            76	 	                                                自动换行时每行的最大字符数
mailtype	           text	                      text or html	            邮件类型。如果发送的是 HTML 邮件，必须是一个完整的网页， 确保网页中没有使用相对路径的链接和图片地址，它们在邮件中不能正确显示。
charset	         $config['charset']	 	                                        字符集（utf-8, iso-8859-1 等）
validate	          FALSE	                TRUE or FALSE (boolean)	                是否验证邮件地址
priority	            3	                    1, 2, 3, 4, 5	              Email 优先级（1 = 最高. 5 = 最低. 3 = 正常）
crlf	                \n	                "\r\n" or "\n" or "\r"	            换行符（使用 "rn" 以遵守 RFC 822）
newline	                \n	                "\r\n" or "\n" or "\r"	            换行符（使用 "rn" 以遵守 RFC 822）
bcc_batch_mode	      FALSE	                TRUE or FALSE (boolean)	          是否启用密送批处理模式（BCC Batch Mode）
bcc_batch_size	        200	                         None	                        使用密送批处理时每一批邮件的数量
dsn	                  FALSE	                TRUE or FALSE (boolean)	                是否启用服务器提示消息
 */
$config = array(
    'protocol' => 'smtp',
    'smtp_crypto' => 'ssl',
    'smtp_host' => 'smtp.163.com',
    'smtp_port' => '465',
    'smtp_user' => 'wuanlife@163.com',
    'smtp_pass' => 'wuan1234',
    'charset'   =>  'utf-8',
    'wordwrap'  =>  TRUE,
    'mailtype'  =>  'html',
);