<?php
	/**
	  * wechat
	  * Author:renbaoyong@gmail.com
	  * date : 2013-5-12
    */
  require_once('functions.php');
  $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
 
  if (!empty($postStr)){
  	$postObj 	= simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	$mType 		= $postObj->MsgType;

		if($mType =='text')
		{
			$fromUsername 	= $postObj->FromUserName;
			$toUsername 	= $postObj->ToUserName;
			$keyword 		= trim($postObj->Content);
			$time 			= time();
   			if(!empty( $keyword ))
            {  
				   /*
				    先去数据库中查找是否有匹配的关键词
				    再查是否有以此关键词为开头的关键词
				    */
				    $sql="select keyword,title,description,type,picurl,url from articles where keyword='{$keyword}' and keyword_type='equals'";
				    $result=mysql_query($sql); 
					$num = mysql_num_rows($result);
					if($num<1){
						  		$sql="select keyword,title from articles where keyword_type='startwith'";
				             	$result=mysql_query($sql); 
				 
				             	$starwith		=	false;
				             	$starwith_str	=	"";
				             	$starwith_func	= 	"";
				             	while ($row=mysql_fetch_row($result))
								{
				 					$_keyword		= 	$row[0];
									$len 			= 	strlen($_keyword);
									if( substr($keyword,0,$len) == $_keyword )
									{
										$starwith_str 	= $_keyword;
										$starwith_func	= $row[1];
										$starwith		= true;
										break;
									}
								}
								
								if( $starwith )
								{
									 $starwith_func($keyword, $starwith_str, $fromUsername, $toUsername);
								}
								else
								{
									$contentStr	=	$NO_RESPONSE;
									
									$msgType 	= "text";
									$resultStr 	= sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
									echo $resultStr;	
								}
				 
					}else
					{
						//有匹配的数据
						$row			=	mysql_fetch_row($result);
						$title 			=	$row[1];
						$type 			=	$row[3];
						$description 	=	$row[2];
						$time 			=  time();
						$picurl 		=	$row[4];
						$url 			=	$row[5];
						$url = str_replace("[[wxid]]", $fromUsername, $url);		// 替换微信的ID
						if( "text" == $type )
						{
							$contentStr	=	$description;
							$contentStr = str_replace("[[wxid]]", $fromUsername, $contentStr);		// 替换微信的ID
							$msgType 	= "text";
							$resultStr 	= sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							echo $resultStr;
						}
						if( "news" == $type )
						{
									$newsTpl = "<xml>
									<ToUserName><![CDATA[%s]]></ToUserName>
									<FromUserName><![CDATA[%s]]></FromUserName>
									<CreateTime>%s</CreateTime>
									<MsgType><![CDATA[%s]]></MsgType>
									<ArticleCount>1</ArticleCount>
									<Articles>
									<item>
									<Title><![CDATA[%s]]></Title>
									<Description><![CDATA[%s]]></Description>
									<PicUrl><![CDATA[%s]]></PicUrl>
									<Url><![CDATA[%s]]></Url>
									</item>
									</Articles>
									<FuncFlag>1</FuncFlag>
									</xml>"; 
									$msgType1  = "news";
									$resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $msgType1, $title,$description,$picurl,$url);
									echo $resultStr;
						}
						mysql_query("insert into messages (uid,content) values('{$fromUsername}','{$keyword}')");
					}
            }
	              	 
		                
	    }elseif($mType =='event')
	    {
	    	$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$location_x = $postObj->Location_X;
			$location_y = $postObj->Location_Y;
			$scale = $postObj->Scale;
			$label = $postObj->Label;
			$time = time();

			 $contentStr =$WELCOME_MSG;   
			 $msgType = "text";
			 $sql1="select * from sj where wxid='{$fromUsername}'";
             $result1=mysql_query($sql1);
             $num1 = mysql_num_rows($result1);
			 if($num1<1)
			 mysql_query("insert into sj (wxid) values('{$fromUsername}')");
			
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
   			echo $resultStr;
	    }
	    elseif($mType =='location')
	    {
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$location_x = $postObj->Location_X;
			$location_y = $postObj->Location_Y;
			$scale = $postObj->Scale;
			$label = $postObj->Label;
			$time = time();
			$contentStr = "您更新了您的位置信息";              
			$msgType = "text";
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			mysql_query("update sj set x='{$location_x}',y='{$location_y}',dz='{$label}',last_update='{$time}' where wxid='{$fromUsername}'");
			echo $resultStr;
	     }
       
	}else {
		echo "";
		exit;
	}


?>
