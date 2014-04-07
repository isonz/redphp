<style>
.bglogin {
	display:none;
	background-color: #E5E5E5;
	width: 100%;
	height: 100%;
	left: 0;
	top: 0;
	filter: alpha(opacity=50);
	opacity: 0.5;
	z-index: 9999;
	position: fixed!important;
	position: absolute;
	_top: 
	expression(eval(document.compatMode &&
	 document.compatMode=='CSS1Compat') ?
	 documentElement.scrollTop + (document.documentElement.clientHeight-this.offsetHeight)/2 :/*IE6*/
	 document.body.scrollTop + (document.body.clientHeight - this.clientHeight)/2)
}
.top-box { left:50%; top:50%; position:absolute; z-index:10000;}
</style>
<!-- -------------------------------------------------------------------------------------------------------- -->

<div>
<!--  <form method="post" action="options.php"> -->
<?php wp_nonce_field('update-options'); ?>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><h3>代理邮件发送设置</h3></td>
  </tr>
  <tr>
    <td height="40">代理URL &nbsp;&nbsp;&nbsp;&nbsp;：<input name="ext_function_mail_proxy" type="text" id="ext_function_mail_proxy" value="<?php echo trim(get_option('ext_function_mail_proxy')); ?>" size="80" />
    </td>
  </tr>
   <tr>
    <td height="40">对方通信密钥：<input name="ext_function_mail_proxy_key" type="text" id="ext_function_mail_proxy_key" value="<?php echo trim(get_option('ext_function_mail_proxy_key')); ?>" size="80" />
    </td>
  </tr>
  <tr>
    <td height="100">
    	<font color="#FF0000"><b>注意:</b><br />此设置是对整个网站的全局设置，如填写了此内容，则本站所有使用了 sMail 类的邮件都将通过此邮件地址发送。<br />
    	如果不需要代理，请将 “代理URL” 留空。</font>
       <br />
       <br />
       代理URL：对方提供给你的通信 URL 地址。<br />
       对方通信密钥：对方提供给你的用来授权的密钥。<br />
       <br />
        <i><b>另：</b>本通信使用的是 UTF-8 编码，如果双方编码不相同，请协商转换编码。</i>
   </td>
  </tr>
  <tr>
    <td height="50">
        <input type="hidden" name="action" value="update" id="action" />
        <!-- <input type="hidden" name="page_options" value="downloadByEmail_emailsettingsubject" />
        <input type="hidden" name="page_options" value="downloadByEmail_emailsettingbody" />
        <input type="submit" value="保存设置" class="button-primary" onclick="emailsubmits()" /> -->
        <input type="button" value="保存设置" id="save_button" class="button-primary" onclick="mailProxy()" />
    </td>
  </tr>
</table>
<!-- </form> -->
</div> 

<hr />

<div>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><h3>做为邮件代理服务器设置</h3></td>
  </tr>
  <tr>
    <td height="40">自身通信密钥：<input name="ext_function_mail_proxy_selfkey" type="text" id="ext_function_mail_proxy_selfkey" value="<?php echo trim(get_option('ext_function_mail_proxy_selfkey')); ?>" size="80" /> &nbsp;&nbsp;&nbsp;<input name="button1" type="button" onclick="randKey()" value="生产密匙"  /> <br /> <br />
    </td>
  </tr>
  <tr>
    <td height="30">
  	本服务器代理URL： <input name="ext_function_mail_proxy_selfurl" id="ext_function_mail_proxy_selfurl" disabled="disabled" type="text" value="<?php echo "http://".$_SERVER['HTTP_HOST']."/?extend-functions-url=ext-function-setting-save"; ?>" size="90" />
  </td>
  </tr>
  <tr>
    <td height="50">        
        <input type="button" value="保存设置" id="save_button2" class="button-primary" onclick="selfmailProxy()" />
    </td>
  </tr>
</table>
</div> 
<hr />
 
 <!-- ---------------------------------------------------- -->
<div id="ajaxstatus" class="bglogin"><div class="top-box" id="statuscontent">
正在提交,请稍候....<br />
<img src="<?php echo downloadByEmail_tempaltePath() . "images/loading.gif"; ?>" border="0" />
</div></div>

<script language="javascript">
var $j = jQuery.noConflict();
var sumbit_status = 0;
var sumbit_all = 0;
function mailProxy()
{
	var selfurl = $j.trim($j("#ext_function_mail_proxy_selfurl").val());
	var proxyurl = $j.trim($j("#ext_function_mail_proxy").val());
	if(selfurl == proxyurl){
		alert('URL 不能填本服务器的URL！');
		return false;	
	}
	
	//-----------------------------------
	$j("#ajaxstatus").show();
	var page_options = [
		['ext_function_mail_proxy', proxyurl],
		['ext_function_mail_proxy_key', $j.trim($j("#ext_function_mail_proxy_key").val())]
	];
	sumbit_all = page_options.length;
	for(var i=0; i<sumbit_all; i++){
		page_options_submits(page_options[i], $j("#_wpnonce").val(), $j("#action").val());
	}										
}

function selfmailProxy()
{
	$j("#ajaxstatus").show();
	var page_options = [
		['ext_function_mail_proxy_selfkey', $j.trim($j("#ext_function_mail_proxy_selfkey").val())]
	];
	sumbit_all = page_options.length;
	for(var i=0; i<sumbit_all; i++){
		page_options_submits(page_options[i], $j("#_wpnonce").val(), $j("#action").val());
	}										
}

//--------------------------------------------------
function page_options_submits(page_option, _wpnonce, action)
{	
	$j.ajax({
	   type: "POST",
	   url: "options.php",
	   data: "page_options="+page_option[0]+"&"+page_option[0]+"="+page_option[1]+"&_wpnonce="+_wpnonce+"&action="+action,
	   success: function(msg){
		  sumbit_status++;
		  if(sumbit_status >= sumbit_all){
			  var cont = $j("#statuscontent").html();
			  $j("#statuscontent").html("请求处理完毕！");
			  setTimeout("ajaxstatus_hide()", 3000);
			  $j("#statuscontent").html(cont);
		  }
	   }
	});
}

function ajaxstatus_hide()
{
	$j("#ajaxstatus").hide();
}

function randKey()
{
	//var key = Math.floor(Math.random()*1234567890)+9876543210;
	var len = 32;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'; // 默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1
    var maxPos = $chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
	$j("#ext_function_mail_proxy_selfkey").val(pwd);
}

</script>
