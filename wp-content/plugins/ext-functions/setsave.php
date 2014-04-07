<?php
$page_id = isset($_REQUEST['page_id']) ? $_REQUEST['page_id'] : null;
if(!$page_id) exit("No Page ID!");

switch($page_id)
{
	case 'mail-proxy':
		mailProxy();
		break;
	default:
		exit('No the page id');
}

function mailProxy()
{
	$proxy_key = isset($_REQUEST['proxy_key']) ? $_REQUEST['proxy_key'] : null;
	$flag = isset($_REQUEST['flag']) ? $_REQUEST['flag'] : null;
	$smtpemailto = isset($_REQUEST['smtpemailto']) ? $_REQUEST['smtpemailto'] : null;
	$mailsubject = isset($_REQUEST['mailsubject']) ? $_REQUEST['mailsubject'] : null;
	$mailbody = isset($_REQUEST['mailbody']) ? ($_REQUEST['mailbody'].".") : null;
	
	if(!$proxy_key || !$smtpemailto || !$mailsubject || !$mailbody) return false;
	$self_proxy_key = get_option('ext_function_mail_proxy_selfkey');
	if($proxy_key != $self_proxy_key) return false;
	
	$smtpserver = isset($_REQUEST['smtpserver']) ? $_REQUEST['smtpserver'] : null;
	$smtpserverport = isset($_REQUEST['smtpserverport']) ? $_REQUEST['smtpserverport'] : null;
	$smtpusermail = isset($_REQUEST['smtpusermail']) ? $_REQUEST['smtpusermail'] : null;
	$smtpuser = isset($_REQUEST['smtpuser']) ? $_REQUEST['smtpuser'] : null;
	$smtppass = isset($_REQUEST['smtppass']) ? $_REQUEST['smtppass'] : null;
	
	$mail = new sMail($smtpemailto, $mailsubject, $mailbody);
	$mail->setSmtpserver($smtpserver);
	$mail->setSmtpserverport($smtpserverport);
	$mail->setSmtpusermail($smtpusermail);
	$mail->setSmtpuser($smtpuser);
	$mail->setSmtppass($smtppass);
	$mail->flag = $flag;
	
	if(!$mail->send()) return false;
	echo $proxy_key;
}
