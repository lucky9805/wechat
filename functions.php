<?php
  /**
  * wechat
  * Author:renbaoyong@gmail.com
  * date : 2013-5-12
  */
  
//*************************里面的内容根据自己实际情况自行修改**************************// 	
	$_hostname	= 'localhost'; 
	$_username	= 'root';    	//你的数据库的用户名
	$_password	= '111111';		//你的数据库的密码
	$_dbname	= "yourdb";	    //数据库的名称

	//用户关注后的欢迎词，根据自己的微信公众账号修改
	$WELCOME_MSG = "很高兴您使用本系统，我们将为您提供更好的信息服务，您可以输入?获取更多帮助";
	//用户输入的关键词系统中还没有 ，进行信息提示
	$NO_RESPONSE = "我们暂时还不提供您要查询的内容，请输入?,获取更多的内容";
//***********************************************************************************//	

	
    $textTpl 	=  "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";  
	$conn = mysql_connect($_hostname, $_username, $_password);
	mysql_select_db($_dbname, $conn);
	mysql_query("set names utf-8");// connect to db
 
 	//自定义的方法都可以写到这个里面来
	function show($keyword, $key, $fromUsername, $toUsername)
	{
		
		$textTpl 	=  "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[%s]]></MsgType>
		<Content><![CDATA[%s]]></Content>
		<FuncFlag>0</FuncFlag>
		</xml>";  
		$time 		= time();
		$msgType 	= "text";
		$contentStr	= "您输入的是，这是一个自定义的方法：".$keyword;
		$resultStr 	= sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		echo $resultStr;
		mysql_query("insert into messages (uid,content) values('{$fromUsername}','{$keyword}')");
	}
	
	function yourfunction($keyword, $key, $fromUsername, $toUsername)
	{
		//..........
	}
?>
