<?php
/*
error_reporting(E_ALL); 
ini_set("display_startup_errors","1"); 
ini_set("display_errors","On");
ini_set("allow_url_fopen", "On");
*/


//$mail = new sMail($mailto, $mail_title, $mail_body);
//if($mail->send()){ }

set_time_limit(300);
include_once 'stmp.php';

class sMail
{
	private $smtpserver = "smtp.126.com";
	private $smtpserverport = 25;
	private $smtpusermail = "sansmovie@126.com";
	private $smtpuser = "sansmovie";
	private $smtppass = "www.3smovie.com";
	
	public $mailtype = 'HTML';
	public $mailsubject = null;
	public $mailbody = null;
	public $smtpemailto = null;
	public $flag = 0;  //0:smtp, 1:mail()
	
	function __construct($smtpemailto, $mailsubject, $mailbody, $flag = 0)
	{
		$this->smtpemailto = $smtpemailto;
		$this->mailsubject = $mailsubject;
		$this->mailbody = wordwrap($mailbody, 60, "\n");   //mail max 70 char
		$this->flag = $flag;
		
		$this->customerConfigServer();
	}
	
	public function send()
	{
		$proxy_url = trim(get_option('ext_function_mail_proxy'));
		$proxy_key = trim(get_option('ext_function_mail_proxy_key'));
		if($proxy_url && $proxy_key) return $this->proxySend($proxy_url, $proxy_key);
		if(!$this->flag){
			$smtp = new smtp($this->smtpserver,$this->smtpserverport,true,$this->smtpuser,$this->smtppass); //这里面的一个true是表示使用身份验证,否则不使用身份验证.
			//$smtp->debug = TRUE;     //是否显示发送的调试信息
			$status = $smtp->sendmail($this->smtpemailto, $this->smtpusermail, $this->mailsubject, $this->mailbody, $this->mailtype);
			return $status;
		}else{
			$headers = "From: $this->smtpusermail";
			return mail($this->smtpemailto, $this->mailsubject, $this->mailbody, $headers);
		}
	}
	
	public function customerConfigServer()
	{
		$smtpserver = trim(get_option('downloadByEmail_mailhost'));
		$smtpserverport = trim(get_option('downloadByEmail_mailport'));
		$smtpusermail = trim(get_option('downloadByEmail_mailaddr'));
		$smtpuser = trim(get_option('downloadByEmail_mailaccount'));
		$smtppass = trim(get_option('downloadByEmail_mailpasswd'));
		
		if($smtpserver)	$this->setSmtpserver($smtpserver);
		if($smtpserverport) $this->setSmtpserverport($smtpserverport);
		if($smtpusermail) $this->setSmtpusermail($smtpusermail);
		if($smtpuser) $this->setSmtpuser($smtpuser);
		if($smtppass) $this->setSmtppass($smtppass);
	}
	
	public function proxySend($proxy_url, $proxy_key)
	{
		if(!$proxy_url || !$proxy_key) return false;
		$self_proxy_key = get_option('ext_function_mail_proxy_selfkey');
		if($self_proxy_key == $proxy_key) return false;   //can not proxy to self;
		
		$req = "page_id=mail-proxy&proxy_key=$proxy_key";
		$req .= "&flag=".$this->flag;
		$req .= "&smtpserver=".$this->smtpserver;
		$req .= "&smtpserverport=".$this->smtpserverport;
		$req .= "&smtpusermail=".$this->smtpusermail;
		$req .= "&smtpuser=".$this->smtpuser;
		$req .= "&smtppass=".$this->smtppass;
		$req .= "&smtpemailto=".urlencode($this->smtpemailto);
		$req .= "&mailsubject=".urlencode($this->mailsubject);
		$req .= "&mailbody=".urlencode($this->mailbody);

		$response = postCurl($proxy_url, $req);
		if(!$response) $response = getCurl($proxy_url, $req);
		if($proxy_key != trim($response)) return false;

		return $response;
	}
	
	public function setSmtpserver($smtpserver)
	{
		$this->smtpserver = $smtpserver;
	}
	
	public function setSmtpserverport($smtpserverport)
	{
		$this->smtpserverport = $smtpserverport;
	}
	
	public function setSmtpusermail($smtpusermail)
	{
		$this->smtpusermail = $smtpusermail;
	}
	
	public function setSmtpuser($smtpuser)
	{
		$this->smtpuser = $smtpuser;
	}
	
	public function setSmtppass($smtppass)
	{
		$this->smtppass = $smtppass;
	}
	
}
