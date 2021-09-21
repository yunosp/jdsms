<?php
//###########**********微信推送部分***********#########################
//推送信息
//#############修改推送参数################
$userid="@all";
$agentid="1000001";//替换你的
$corpid = "替换你的 企业ID";
$corpsecret = "替换你的 corpsecret";


//#################end####################


function getPush($message) {
	global $userid;
	global $agentid;
	$userinfo = getToken();
	//获取access_token
	$access_token = $userinfo['access_token'];
	$sendmsg_url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$access_token;
	$data="{\"touser\":\"$userid\",\"msgtype\":\"text\",\"agentid\":$agentid,\"text\":{\"content\":\"$message\"},\"safe\":0}";
	$res = curlPost($sendmsg_url,$data);
	$errmsg=json_decode($res)->errmsg;
	if($errmsg==="ok") {
		;
		return 1;
		//推送成功
	} else {
		return 0;
		//推送失败
	}
}
//获取token
function getToken() {
	global $corpid;
	global $corpsecret;
	
	$Url="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$corpid."&corpsecret=".$corpsecret;
	$res = curlPost($Url);
	$access_token=json_decode($res)->access_token;
	$userinfo = array();
	$userinfo['access_token']=$access_token;
	return $userinfo;
}
//定义curl方法
function curlPost($url,$data="") {
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
	if ($ssl) {
		$opt[CURLOPT_SSL_VERIFYHOST] = 2;
		$opt[CURLOPT_SSL_VERIFYPEER] = FALSE;
	}
	curl_setopt_array($ch,$opt);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
//###########**********微信推送部分***********#########################