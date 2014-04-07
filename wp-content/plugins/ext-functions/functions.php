<?php
//$file = "./config.csv";	$data = getCSV($file);
function getCSV($file_path, $start_line = 2)
{
	if(!realpath($file_path)) exit('No CSV file found!');
	
	$data = array();
	$row = 0;
	$handle = fopen($file_path, "r");
	while ($line = fgetcsv($handle, 2000, ",")) {
		$row++;
		if($row < $start_line) continue;
		
		$key = isset($line[0]) ? $line[0] : 0;
		if(!$key) continue;
		
		foreach($line as $k=>$v){
			$line[$k] = iconv('GB2312', 'UTF-8', $v);
		}
		
		$data[$key] = $line;
	}
	fclose($handle);
	
	return $data;
}

//导出CSV
function exportCSV($filename,$data) {
	header("Content-type:text/csv");
	header("Content-Disposition:attachment;filename=".$filename);
	header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	header('Expires:0');
	header('Pragma:public');
	echo $data;
}

//获取IP地址
function getIP()
{
	if (isSet($_SERVER)) {
		if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if ( getenv('HTTP_X_FORWARDED_FOR' ) ) {
			$realip = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$realip = getenv('HTTP_CLIENT_IP' );
		} else {
			$realip = getenv('REMOTE_ADDR' );
		}
	}
	return $realip;
}

//获取当前完整的带参数的URL
function getCurrentURL()
{
	$pageURL = 'http';

	if ($_SERVER["HTTPS"] == "on")
	{
		$pageURL .= "s";
	}
	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80")
	{
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	}
	else
	{
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

//分页 :URL, 记录总数， 当前页， 每页数, 方块数
function pager($url, $total = 1, $page = 1, $pagesize=20, $page_tab = 9)
{
	if(!$url) $url = "http://".$_SERVER["HTTP_HOST"];
	$pager = "<style>
		#pager{line-height:30px; width:100%; color:#666; text-align:center}
		#pager a{border:1px solid #666; margin-right:5px; padding:2px 5px 2px 5px; color:#666; text-decoration:none;}
		#pager .active{background:#6C0; color:#FFF}
		</style>";
	
	$page = (int)$page;
	$pagesize = (int)$pagesize;
	if($page < 1) $page = 1;
	if($pagesize < 1) $pagesize = 1;
	
	$start = ($page-1) * $pagesize;
	$end = $page * $pagesize;
	$limit = array($start, $end);

	$total_page = ceil($total/$pagesize);
	if($page > $total_page) $page=$total_page;
	
	$page_p = $page - 1;
	$page_n = $page + 1;

	$pager .="总共 $total 条记录，共 $total_page 页 。<a href='$url&pagesize=$pagesize&p=1'>首页</a>";
	if($page > 1) $pager .="<a href='$url&pagesize=$pagesize&p=$page_p'>上一页</a>";
	if($total_page <= $page_tab){
		for($i=1; $i<=$total_page; $i++){
			if($i == $page){
				$active = ' class="active" ';
			}else{
				$active = null;
			}
			$pager .="<a href='$url&pagesize=$pagesize&p=$i' $active>$i</a>";
		}
	}else{
		$s = $page - floor($page_tab/2);
		$e = $page + floor($page_tab/2);
		if($s < 1) $s = 1;
		if($e > $total_page) $e = $total_page;
		if($s > 1) $pager .="<a href='$url&pagesize=$pagesize&p=1'>1</a> ... ";
		
		for($i=$s; $i<=$e; $i++){
			if($i == $page){
				$active = ' class="active" ';
			}else{
				$active = null;
			}
			$pager .="<a href='$url&pagesize=$pagesize&p=$i' $active>$i</a>";
		}
		
		if($e < $total_page) $pager .=" ... <a href='$url&pagesize=$pagesize&p=$total_page'>$total_page</a>";
	}
	if($page < $total_page) $pager .="<a href='$url&pagesize=$pagesize&p=$page_n'>下一页</a>";
	$pager .="<a href='$url&pagesize=$pagesize&p=$total_page'>末页</a>";
	
	$pager = "<div id='pager'>$pager</div>";
	return $pager;
}

//---------------------------------------------------------------------- CURL
//$uri = "https://www.paypal.com/cgi-bin/webscr";
//$req .= '&' . $key . '=' . urlencode($value);
//$response = postCurl($uri, $req);
//if(0 == strcmp('VERIFIED', $response)) $verified = 1;

//CURL
function postCurl($uri, $req)
{
	$response = null;
	if(!$uri || !$req) return false;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_PORT, $_SERVER['SERVER_PORT']);
	$response = curl_exec($ch);
	curl_close ($ch);
	return $response;
}

//get
function getCurl($uri, $req)
{
   	$contents = file_get_contents($uri."&".$req);
	return $contents;
}
//---------------------------------------------------------------------- END CURL
